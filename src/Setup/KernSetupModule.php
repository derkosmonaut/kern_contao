<?php

declare(strict_types=1);

namespace Derkosmonaut\KernContaoBundle\Setup;

use Contao\BackendUser;
use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
use Contao\Message;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Backend-Aktion „KERN-Grundeinrichtung“ (Layout → Themes → Button oben).
 *
 * Legt in einem Schritt an:
 * - Theme „KERN UX“ mit Seitenlayout (KERN aktiv) und Modulen
 *   (Seitenname, Servicelinks, Darstellung, Hauptnavigation, Breadcrumb,
 *   Footer-Navigation, Suche)
 * - optional einen Seitenbaum mit Startseite, Kontakt, Leichte Sprache,
 *   Gebärdensprache, Impressum, Datenschutz, Erklärung zur Barrierefreiheit
 *   (mit Formular „Barriere melden“), Suche und 404-Seite
 *
 * Wird nur ausgeführt, wenn es noch kein Theme „KERN UX“ gibt, und legt den
 * Seitenbaum nur an, wenn noch keine Startpunkte existieren.
 */
final class KernSetupModule
{
    public const THEME_NAME = 'KERN UX';

    private const FORM_SUBMIT = 'kern_setup';

    public function __construct(
        private readonly Connection $connection,
        private readonly Security $security,
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $router,
        private readonly ContaoCsrfTokenManager $csrfTokenManager,
    ) {
    }

    public function run(): RedirectResponse|string
    {
        $lang = &$GLOBALS['TL_LANG']['tl_theme'];
        $backUrl = $this->router->generate('contao_backend', ['do' => 'themes']);

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            Message::addError($lang['kern_setup_admin'] ?? 'Nur Administratoren können die Grundeinrichtung ausführen.');

            return new RedirectResponse($backUrl);
        }

        if ($this->themeExists()) {
            Message::addInfo($lang['kern_setup_exists'] ?? 'Es gibt bereits ein Theme „KERN UX“.');

            return new RedirectResponse($backUrl);
        }

        $request = $this->requestStack->getCurrentRequest();
        $hasRoots = $this->rootPagesExist();

        if ($request && self::FORM_SUBMIT === $request->request->get('FORM_SUBMIT')) {
            $siteName = trim((string) $request->request->get('site_name')) ?: 'Meine Website';
            $email = trim((string) $request->request->get('email'));
            $createPages = !$hasRoots && $request->request->get('create_pages');

            if ('' !== $email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Message::addError($lang['kern_setup_email_invalid'] ?? 'Bitte eine gültige E-Mail-Adresse angeben.');
            } else {
                $this->connection->transactional(fn () => $this->setup($siteName, $email, (bool) $createPages));

                Message::addConfirmation($createPages
                    ? ($lang['kern_setup_done_pages'] ?? 'Grundeinrichtung angelegt. Bitte alle Texte mit [eckigen Klammern] prüfen und anpassen.')
                    : ($lang['kern_setup_done'] ?? 'Theme, Seitenlayout und Module angelegt.'));

                return new RedirectResponse($backUrl);
            }
        }

        return $this->renderForm($backUrl, $hasRoots);
    }

    private function renderForm(string $backUrl, bool $hasRoots): string
    {
        $lang = $GLOBALS['TL_LANG']['tl_theme'];
        $e = static fn (string $key, string $fallback) => StringUtil::specialchars($lang[$key] ?? $fallback);
        $token = StringUtil::specialchars($this->csrfTokenManager->getDefaultTokenValue());

        $pages = $hasRoots
            ? '<p class="tl_info">'.$e('kern_setup_has_roots', 'Es gibt bereits Startpunkte. Der Seitenbaum wird deshalb nicht angelegt, nur Theme, Seitenlayout und Module.').'</p>'
            : '<div class="widget">
    <div class="tl_checkbox_single_container">
      <input type="checkbox" name="create_pages" id="kern_create_pages" class="tl_checkbox" value="1" checked>
      <label for="kern_create_pages">'.$e('kern_setup_pages', 'Seitenbaum mit Pflichtseiten und Platzhaltertexten anlegen').'</label>
    </div>
  </div>';

        return Message::generate().'
<div id="tl_buttons">
  <a href="'.StringUtil::specialchars($backUrl).'" class="header_back" accesskey="b">'.($GLOBALS['TL_LANG']['MSC']['backBT'] ?? 'Zurück').'</a>
</div>
<form id="kern_setup" class="tl_form tl_edit_form" method="post" data-turbo="false">
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="'.self::FORM_SUBMIT.'">
<input type="hidden" name="REQUEST_TOKEN" value="'.$token.'">

<div class="tl_tbox">
  <p>'.$e('kern_setup_intro', 'Legt das Theme „KERN UX“ mit Seitenlayout und Modulen an. Bestehende Inhalte werden nicht verändert.').'</p>
  <div class="widget w50">
    <h3><label for="kern_site_name">'.$e('kern_setup_site_name', 'Name der Website').'</label></h3>
    <input type="text" name="site_name" id="kern_site_name" class="tl_text" maxlength="255" required>
  </div>
  <div class="widget w50">
    <h3><label for="kern_email">'.$e('kern_setup_email', 'E-Mail für Meldungen von Barrieren').'</label></h3>
    <input type="email" name="email" id="kern_email" class="tl_text" maxlength="255">
    <p class="tl_help tl_tip">'.$e('kern_setup_email_help', 'Empfänger des Formulars „Barriere melden“. Kann später im Formular geändert werden.').'</p>
  </div>
  <div class="clr">'.$pages.'</div>
</div>

</div>

<div class="tl_formbody_submit">
<div class="tl_submit_container">
  <button type="submit" name="save" id="save" class="tl_submit" accesskey="s">'.$e('kern_setup_submit', 'Grundeinrichtung anlegen').'</button>
</div>
</div>
</form>';
    }

    private function setup(string $siteName, string $email, bool $createPages): void
    {
        $themeId = $this->insert('tl_theme', [
            'name' => self::THEME_NAME,
            'author' => 'kern_contao',
        ]);

        $module = fn (string $name, string $type, array $fields = []) => $this->insert('tl_module', [
            'pid' => $themeId,
            'name' => $name,
            'type' => $type,
            ...$fields,
        ]);

        $siteNameId = $module('Seitenname', 'html', [
            'html' => '<a class="kern-site-name" href="{{env::path}}">{{page::rootPageTitle}}</a>',
        ]);
        $serviceId = $module('Servicelinks (Leichte Sprache, Gebärdensprache)', 'kern_service_links');
        $displayId = $module('Darstellung anpassen', 'kern_display');
        $navId = $module('Hauptnavigation', 'navigation', ['ariaLabel' => 'Hauptnavigation', 'showLevel' => 2]);
        $breadcrumbId = $module('Breadcrumb', 'breadcrumb');
        $footerNavId = $module('Footer-Navigation', 'customnav', ['ariaLabel' => 'Service']);
        $searchId = $module('Suche', 'search', ['perPage' => 10, 'searchType' => 'simple', 'headline' => serialize(['value' => '', 'unit' => 'h2'])]);

        $layoutId = $this->insert('tl_layout', [
            'pid' => $themeId,
            'name' => 'KERN Standard',
            'type' => 'default',
            'rows' => '3rw',
            'cols' => '1cl',
            'framework' => serialize([]),
            'template' => 'fe_page',
            'kern_ux' => 1,
            'modules' => serialize([
                ['mod' => $siteNameId, 'col' => 'header', 'enable' => 1],
                ['mod' => $serviceId, 'col' => 'header', 'enable' => 1],
                ['mod' => $displayId, 'col' => 'header', 'enable' => 1],
                ['mod' => $navId, 'col' => 'header', 'enable' => 1],
                ['mod' => $breadcrumbId, 'col' => 'header', 'enable' => 1],
                ['mod' => 0, 'col' => 'main', 'enable' => 1],
                ['mod' => $footerNavId, 'col' => 'footer', 'enable' => 1],
            ]),
        ]);

        if (!$createPages) {
            return;
        }

        $pages = (new KernSetupContent())->pages($siteName);
        $rootId = $this->insert('tl_page', [
            'pid' => 0,
            'sorting' => 128,
            'type' => 'root',
            'title' => $siteName,
            'alias' => StringUtil::generateAlias($siteName) ?: 'website',
            'language' => 'de',
            'fallback' => 1,
            'includeLayout' => 1,
            'layout' => $layoutId,
            'robots' => 'index,follow',
            'published' => 1,
        ]);

        $formId = $this->createFeedbackForm($email);
        $ids = [];
        $sorting = 0;

        foreach ($pages as $key => $page) {
            $sorting += 128;

            $ids[$key] = $this->insert('tl_page', [
                'pid' => $rootId,
                'sorting' => $sorting,
                'type' => $page['type'] ?? 'regular',
                'title' => $page['title'],
                'alias' => $page['alias'],
                'hide' => (int) ($page['hide'] ?? false),
                'robots' => $page['robots'] ?? 'index,follow',
                'published' => 1,
            ]);

            $articleId = $this->insert('tl_article', [
                'pid' => $ids[$key],
                'sorting' => 128,
                'title' => $page['title'],
                'alias' => $page['alias'],
                'author' => (int) (BackendUser::getInstance()->id ?? 0),
                'inColumn' => 'main',
                'published' => 1,
            ]);

            $elementSorting = 0;

            foreach ($page['elements'] as $element) {
                $elementSorting += 128;

                $row = [
                    'pid' => $articleId,
                    'ptable' => 'tl_article',
                    'sorting' => $elementSorting,
                    'type' => $element['type'] ?? 'text',
                ];

                if (isset($element['headline'])) {
                    $row['headline'] = serialize(['value' => $element['headline'], 'unit' => $element['unit'] ?? 'h2']);
                }

                if (isset($element['text'])) {
                    $row['text'] = $element['text'];
                }

                if (isset($element['alert'])) {
                    $row['kern_alert_style'] = $element['alert'];
                }

                match ($element['type'] ?? 'text') {
                    'form' => $row['form'] = $formId,
                    'module' => $row['module'] = $searchId,
                    default => null,
                };

                $this->insert('tl_content', $row);
            }
        }

        // Footer-Navigation, Servicelinks und Suche mit den neuen Seiten verknüpfen
        $this->update('tl_module', ['pages' => serialize(array_map('intval', [
            $ids['kontakt'], $ids['impressum'], $ids['datenschutz'], $ids['barrierefreiheit'], $ids['suche'],
        ]))], $footerNavId);

        $this->update('tl_module', [
            'kern_easy_language' => $ids['leichte_sprache'],
            'kern_sign_language' => $ids['gebaerdensprache'],
        ], $serviceId);

        $this->update('tl_module', ['jumpTo' => $ids['suche']], $searchId);
    }

    private function createFeedbackForm(string $email): int
    {
        $formId = $this->insert('tl_form', [
            'title' => 'Barriere melden',
            'alias' => 'barriere-melden',
            'ajax' => 1,
            'confirmation' => '<p>Vielen Dank für Ihre Meldung. Wir prüfen sie und melden uns, wenn Sie eine E-Mail-Adresse angegeben haben.</p>',
            'sendViaEmail' => '' !== $email ? 1 : 0,
            'recipient' => $email,
            'subject' => 'Meldung einer Barriere',
            'format' => 'raw',
        ]);

        $fields = [
            ['type' => 'explanation', 'text' => '<p>Ist Ihnen auf unserer Website eine Barriere aufgefallen? Beschreiben Sie bitte kurz, wo und welches Problem aufgetreten ist. Angaben zu Ihrer Person sind freiwillig.</p>'],
            ['type' => 'textarea', 'name' => 'meldung', 'label' => 'Beschreibung der Barriere', 'mandatory' => 1],
            ['type' => 'text', 'name' => 'seite', 'label' => 'Seite oder Adresse (URL), auf der die Barriere auftritt'],
            ['type' => 'text', 'name' => 'name', 'label' => 'Ihr Name'],
            ['type' => 'text', 'name' => 'email', 'label' => 'Ihre E-Mail-Adresse für Rückfragen', 'rgxp' => 'email'],
            ['type' => 'checkbox', 'name' => 'datenschutz', 'label' => 'Datenschutz', 'mandatory' => 1, 'options' => serialize([['value' => '1', 'label' => 'Ich habe die Datenschutzerklärung gelesen und bin mit der Verarbeitung meiner Angaben zur Bearbeitung der Meldung einverstanden.']])],
            ['type' => 'submit', 'slabel' => 'Barriere melden'],
        ];

        foreach ($fields as $i => $field) {
            $this->insert('tl_form_field', ['pid' => $formId, 'sorting' => ($i + 1) * 128, ...$field]);
        }

        return $formId;
    }

    /**
     * Spaltennamen werden quotiert, weil Contao-Felder wie „rows“ in
     * MySQL/MariaDB reservierte Wörter sind.
     */
    private function insert(string $table, array $row): int
    {
        $row['tstamp'] ??= time();
        $this->connection->insert($this->connection->quoteIdentifier($table), $this->quoteKeys($row));

        return (int) $this->connection->lastInsertId();
    }

    private function update(string $table, array $data, int $id): void
    {
        $this->connection->update($this->connection->quoteIdentifier($table), $this->quoteKeys($data), ['id' => $id]);
    }

    private function quoteKeys(array $row): array
    {
        return array_combine(array_map($this->connection->quoteIdentifier(...), array_keys($row)), $row);
    }

    private function themeExists(): bool
    {
        return false !== $this->connection->fetchOne('SELECT id FROM tl_theme WHERE name = ?', [self::THEME_NAME]);
    }

    private function rootPagesExist(): bool
    {
        return false !== $this->connection->fetchOne("SELECT id FROM tl_page WHERE type = 'root'");
    }
}
