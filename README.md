# kern_contao

KERN UX Integration für Contao CMS 5.

Dieses Bundle bindet das [KERN Plain-CSS-HTML-Kit](https://gitlab.opencode.de/kern-ux/kern-ux-plain) (Version 2.8.2, npm: `@kern-ux/native`) in Contao ein und stellt die Inhalts-Twig-Templates von Contao auf KERN um.

## Inhalt

- `public/kern/` – kompilierte KERN-Assets (`kern.min.css`, Fira-Sans-Schriften als WOFF2, Lizenz unter `public/kern/LICENSE.md`, EUPL-1.2). Die Schriften werden selbst gehostet (DSGVO), nicht über Drittanbieter-CDNs.
- `contao/config/config.php` – registriert die Stylesheets im Frontend (`$GLOBALS['TL_CSS']`).
- `contao/templates/` – Twig-Templates (`.twig-root` markiert das Verzeichnis als Twig-Root). Sie liegen auf denselben logischen Namen wie die Core-Templates (`@Contao/...`) und gewinnen deshalb in der Template-Hierarchie, weil das Bundle nach dem Core-Bundle geladen wird (`setLoadAfter` im Contao-Manager-Plugin).
- `public/css/kern-contao.css` – kleine Ergänzungsschicht, die RTE-Inhalte (`.kern-content`) per KERN-Design-Tokens (CSS Custom Properties) typografisch aufbereitet.

## Voraussetzungen

- Contao 5.7 (Managed Edition), PHP >= 8.2
- Composer und SSH-Zugriff auf den Server (z. B. `kern.bbsmedien.de`)

## Installation Schritt für Schritt

1. **Auf dem Server einloggen** und ins Contao-Projekt wechseln:

   ```bash
   cd /var/www/kern.bbsmedien.de   # Pfad an die Installation anpassen
   ```

2. **Bundle einbinden – direkt über die `composer.json` des Projekts (empfohlen auf dem Server).** Solange das Paket noch nicht auf Packagist liegt, zusätzlich ein `vcs`-Repository auf das GitHub-Repo eintragen und eine Dev-Version verlangen:

   ```json
   {
       "repositories": [
           { "type": "vcs", "url": "https://github.com/derkosmonaut/kern_contao" }
       ],
       "require": {
           "contao/manager-bundle": "5.7.*",
           "derkosmonaut/kern-contao": "dev-main"
       }
   }
   ```

   ```bash
   composer update derkosmonaut/kern-contao
   ```

   Komplettes Beispiel für die `composer.json` des Contao-Projekts (Managed Edition, `public-dir: public`):

   ```json
   {
       "type": "project",
       "require": {
           "contao/conflicts": "@dev",
           "contao/manager-bundle": "5.7.*",
           "contao/calendar-bundle": "5.7.*",
           "contao/comments-bundle": "5.7.*",
           "contao/faq-bundle": "5.7.*",
           "contao/listing-bundle": "5.7.*",
           "contao/news-bundle": "5.7.*",
           "contao/newsletter-bundle": "5.7.*",
           "derkosmonaut/kern-contao": "dev-main"
       },
       "repositories": [
           { "type": "vcs", "url": "https://github.com/derkosmonaut/kern_contao" }
       ],
       "extra": {
           "public-dir": "public",
           "contao-component-dir": "assets"
       },
       "config": {
           "github-protocols": ["https"],
           "allow-plugins": {
               "contao-components/installer": true,
               "contao/manager-plugin": true,
               "php-http/discovery": true
           }
       },
       "scripts": {
           "post-install-cmd": ["@php vendor/bin/contao-setup"],
           "post-update-cmd": ["@php vendor/bin/contao-setup"]
       }
   }
   ```

   Auf dem Server außerdem SSH→HTTPS für GitHub umbiegen: `git config --global url."https://github.com/".insteadOf "git@github.com:"`

   - Ist PR #1 noch nicht in `main` gemerged, stattdessen die Branch-Version verlangen: `"derkosmonaut/kern-contao": "dev-vibe/kern-contao-bundle-c3f5aa"`
   - Sobald das Paket auf Packagist veröffentlicht ist, entfällt der `repositories`-Eintrag und es genügt `"derkosmonaut/kern-contao": "^1.0"` in `require`
   - Auf dem Server braucht Composer Lesezugriff auf `github.com` (HTTPS/Git)

3. **Assets und Cache werden automatisch erledigt.** Wegen `"extra": {"public-dir": "public"}` landen die Bundle-Assets unter `public/bundles/kerncontaobundle/`. Dein `post-install-cmd`/`post-update-cmd` (`@php vendor/bin/contao-setup`) führt automatisch `assets:install --symlink --relative`, `contao:symlinks` und `cache:clear` (prod + dev) aus – kein manueller Schritt nötig. Manuell nachhelfen geht mit:

   ```bash
   vendor/bin/contao-console assets:install --symlink --relative public
   vendor/bin/contao-console cache:clear
   ```

   Hinweise: `"contao-component-dir": "assets"` betrifft nur die `contao-components/*`-Pakete, nicht die Bundle-Assets aus `public/`. Eine Anpassung von `config.allow-plugins` ist nicht nötig – das Bundle registriert kein Composer-Plugin (der Contao-Manager-Plugin wird über `extra.contao-manager-plugin` in der `composer.json` des Bundles erkannt).

4. **Lokale Entwicklung** geht am komfortabelsten über ein `path`-Repository mit Symlink (Änderungen am Klon greifen sofort):

   ```bash
   composer config repositories.kern-contao '{"type": "path", "url": "/pfad/zum/kern_contao", "options": {"symlink": true}}'
   composer require derkosmonaut/kern-contao:*@dev
   ```

5. **Testen:** Eine Seite mit Inhaltselementen im Frontend aufrufen. Im Seitenquelltext muss im `<head>` stehen:

   ```html
   <link rel="stylesheet" href="https://kern.bbsmedien.de/bundles/kerncontaobundle/kern/fonts/fira-sans.css?...">
   <link rel="stylesheet" href="https://kern.bbsmedien.de/bundles/kerncontaobundle/kern/kern.min.css?...">
   <link rel="stylesheet" href="https://kern.bbsmedien.de/bundles/kerncontaobundle/css/kern-contao.css?...">
   ```

   Die Template-Hierarchie lässt sich prüfen mit:

   ```bash
   vendor/bin/contao-console debug:contao-twig
   ```

## Installation ohne SSH (nur FTP / Web-Oberfläche)

### Variante 1 (empfohlen): Packagist + Contao Manager

1. Paket auf [packagist.org/packages/submit](https://packagist.org/packages/submit) registrieren (GitHub-Login, Repo-URL `https://github.com/derkosmonaut/kern_contao` – das Repo muss public sein).
   Damit Packagist bei jedem Push automatisch aktualisiert (sonst erscheint „This package is not auto-updated"), zusätzlich den GitHub-Hook einrichten: Repo → **Settings → Webhooks → Add webhook**, Payload URL `https://packagist.org/api/github/hook`, Content type `application/json`, Events: **„Just the push event“**, Active ☑. Zum Sofort-Test: unter „Recent Deliveries“ das letzte Event „Redeliver“ – oder einfach einen Commit pushen. Danach listet die [Paketseite](https://packagist.org/packages/derkosmonaut/kern-contao) die Version `dev-main` (sowie Branch-Versionen `dev-<branch>`).
2. Contao Manager im Browser öffnen: `https://<domain>/contao-manager.phar.php` → „Pakete installieren“ → `derkosmonaut/kern-contao` suchen und installieren (bis zum ersten Release-Tag Version `dev-main`, danach `^1.0`).
3. Der Manager führt `composer`, `contao-setup` (inkl. `assets:install`, `contao:symlinks`, `cache:clear`) serverseitig aus – kein SSH, kein FTP nötig. Assets liegen danach unter `public/bundles/kerncontaobundle/`.

### Variante 2 (sofort, manuell per FTP)

1. ZIP laden und entpacken: `https://github.com/derkosmonaut/kern_contao/archive/refs/heads/main.zip`
2. Inhalt von `kern_contao-main/` per FTP nach `vendor/derkosmonaut/kern-contao/` hochladen.
3. `config/bundles.php` am Ende des Arrays ergänzen:

   ```php
   'Derkosmonaut\\KernContaoBundle\\KernContaoBundle' => ['all' => true],
   ```

4. Autoload ergänzen – in `vendor/composer/autoload_psr4.php` **und** in `vendor/composer/autoload_static.php` (Array `$prefixDirsPsr4`):

   ```php
   'Derkosmonaut\\KernContaoBundle\\' => array($baseDir . '/derkosmonaut/kern-contao/src'),
   ```

5. Assets per FTP kopieren: Inhalt des Bundle-Ordners `public/` hochladen nach `public/bundles/kerncontaobundle/` (also `public/kern/...` → `public/bundles/kerncontaobundle/kern/...` und `public/css/kern-contao.css` → `public/bundles/kerncontaobundle/css/kern-contao.css`). Das beseitigt die 404s auf `/bundles/kerncontaobundle/...`.
6. Cache leeren per FTP: Inhalt von `var/cache/prod/` und `var/cache/dev/` löschen (Ordner werden beim nächsten Aufruf neu aufgebaut).
7. Test im Browser: `https://<domain>/bundles/kerncontaobundle/kern/kern.min.css` muss HTTP 200 liefern, im Seitenquelltext die drei `<link>`-Tags vorhanden sein.

Hinweis: Variante 2 ist eine manuelle Installation – sobald wieder `composer` läuft (z. B. über den Contao Manager), wird `vendor/` und `config/bundles.php` aus `installed.json` neu generiert; das Paket muss dann regulär per Composer/Packagist installiert sein (Variante 1), sonst verschwindet es beim nächsten Update.

## Troubleshooting

### `Failed to clone git@github.com:derkosmonaut/kern_contao.git ... Permission denied (publickey)`

Composer versucht, das `vcs`-Repository per SSH zu klonen, hat aber keinen GitHub-Schlüssel. Ursachen und Lösungen:

1. **Das GitHub-Repo ist privat** – anonyme Klone schlagen fehl. Repo auf GitHub public machen (Settings → Danger Zone → Change visibility) oder eine der Alternativen unten nutzen.
2. **HTTPS statt SSH erzwingen** – in der `composer.json` des Projekts ergänzen:

   ```json
   "config": {
       "github-protocols": ["https"]
   }
   ```

   und auf dem Server zusätzlich: `git config --global url."https://github.com/".insteadOf "git@github.com:"`

   Dann genügt `composer update derkosmonaut/kern-contao` (bei public Repo klappt der anonyme HTTPS-Clone).
3. **Packagist** – sobald das Paket auf [packagist.org](https://packagist.org) registriert ist (dafür muss das Repo public sein), entfällt der `repositories`-Eintrag komplett: `"derkosmonaut/kern-contao": "dev-main"` bzw. nach einem Release-Tag `"^1.0"`. Packagist liefert dann ein dist-ZIP, es wird kein `git clone` ausgeführt.
4. **Ohne GitHub-Zugriff beim Dependency-Resolve** (z. B. eingeschränkter Host): `path`-Repository auf dem Server nutzen – Repo nach `packages/kern-contao` klonen und in der `composer.json`:

   ```json
   "repositories": [
       { "type": "path", "url": "packages/kern-contao", "options": { "symlink": false } }
   ],
   "require": {
       "derkosmonaut/kern-contao": "*@dev"
   }
   ```

### `Template "@Contao_KernContaoBundle/component/_x.html.twig" cannot be used as a trait ...`

Core-Komponenten (`component/_*.html.twig`) werden per `{% use %}` als Traits eingebunden; ein Template mit `{% extends %}` ist laut Twig kein gültiges Trait. Deshalb verwenden die Komponenten-Overrides in diesem Bundle `{% use %}` statt `{% extends %}` (Horizontal Reuse: eigene Blöcke gewinnen gegenüber den importierten Core-Blöcken). Die `content_element/*.html.twig`-Overrides werden dagegen vom Fragment-Controller gerendert und dürfen weiterhin `{% extends '@Contao/...' %}` nutzen.

### `404` auf `/bundles/kerncontaobundle/...` (KERN-CSS/Fonts werden nicht geladen)

Die `<link>`-Tags im `<head>` kommen aus `$GLOBALS['TL_CSS']` und erscheinen immer – die Dateien selbst liegen erst nach `assets:install` unter `public/bundles/kerncontaobundle/`. Prüfung und Fix auf dem Server:

```bash
ls -la public/bundles/ | grep kern

vendor/bin/contao-console assets:install public --symlink --relative   # Symlink-Modus
# falls der Hoster keine Symlinks erlaubt ("symlink method is unavailable"):
vendor/bin/contao-console assets:install public --relative               # Kopier-Modus
vendor/bin/contao-console contao:symlinks public

curl -I https://kern.bbsmedien.de/bundles/kerncontaobundle/kern/kern.min.css   # erwartet: HTTP 200
curl -I https://kern.bbsmedien.de/bundles/kerncontaobundle/kern/fonts/fira-sans/FiraSans-Regular.woff2
```

Voraussetzung: Webserver-Docroot ist `public/` (Contao-5-Standard). Danach Browser-Cache leeren (Hard Reload), da die 404s evtl. gecacht sind. Die Schriften (`fira-sans.css`) referenzieren die WOFF2-Dateien relativ (`./fira-sans/*.woff2`) – das Verzeichnis `public/bundles/kerncontaobundle/kern/fonts/fira-sans/` muss mit installiert sein.

## Welche Templates auf KERN umgestellt werden

| Contao-Inhaltselement / Komponente | KERN-Markup |
| --- | --- |
| Überschrift (`component/_headline`) | `h1`–`h6` mit `kern-heading-display` / `kern-heading-x-large` / `kern-heading-large` / `kern-heading-medium` / `kern-heading-small` |
| Text (Rich Text, `component/_rich_text`) | Wrapper mit `kern-content` (ergänzende Token-Styles, siehe `public/css/kern-contao.css`) |
| Hyperlink | `kern-link` |
| Liste | `kern-list kern-list--bullet` (ul) bzw. `kern-list--number` (ol) |
| Tabelle | `kern-table`, `kern-table__head/__body/__footer/__row/__header/__cell`, Caption mit `kern-title` |
| Download(s) | `kern-link` mit `kern-icon--download` |
| Akkordeon | natives `<details class="kern-accordion">` mit `<summary class="kern-accordion__header">` und `kern-accordion__body` (barrierefrei ohne JavaScript; das Handorgel-JS des Core entfällt) |
| Description List | `kern-description-list` mit `kern-description-list-item__key/__value` |
| Teaser | `kern-card` mit `kern-card__header/__body/__footer`, Link als `kern-btn kern-btn--secondary` |
| Toplink | `kern-link` mit `kern-icon--arrow-up` |
| Galerie | KERN-Grid: `kern-row` mit `kern-col-12 kern-col-sm-6 kern-col-md-N` |
| „Mehr lesen“-Link (`component/_read_more`) | `kern-link` mit `kern-icon--arrow-forward` |

Alle Overrides erweitern die Core-Templates (`{% extends '@Contao/...' %}`) und überschreiben nur einzelne Blöcke, sodass die Core-Logik (Attribute, Barrierefreiheit, Sortierung, Sanitizer) erhalten bleibt.

## Eigene Anpassungen

Eigene Templates gehören in den Ordner `templates/` der Contao-Installation (höchste Priorität in der Hierarchie), z. B. `templates/content_element/text.html.twig`. Dort können einzelne Blöcke per `{% extends '@Contao/content_element/text.html.twig' %}` angepasst werden. Varianten (Auswahl im Backend) liegen als Unterordner, z. B. `templates/content_element/text/hervorgehoben.html.twig`.

## KERN aktualisieren

Die Assets stammen aus dem Release von [kern-ux-plain](https://gitlab.opencode.de/kern-ux/kern-ux-plain) bzw. dem npm-Paket `@kern-ux/native`:

```bash
npm pack @kern-ux/native@<VERSION>
tar xzf kern-ux-native-<VERSION>.tgz
cp package/dist/kern.min.css public/kern/kern.min.css
cp package/dist/fonts/fira-sans.css public/kern/fonts/
cp package/dist/fonts/fira-sans/*.woff2 public/kern/fonts/fira-sans/
cp package/LICENSE.md public/kern/LICENSE.md
```

Danach `vendor/bin/contao-console cache:clear` ausführen. KERN-Klassenreferenz: [KERN UX Cheatsheet](https://digitalservicebund.github.io/kern-ux-cheatsheet/) und [kern-ux.de](https://www.kern-ux.de/komponenten/).

## Lizenz

- PHP-/Template-Code dieses Bundles: GPL-3.0-or-later (siehe `LICENSE`)
- Die eingebetteten KERN-Assets (`public/kern/`): EUPL-1.2 (siehe `public/kern/LICENSE.md`)
