# kern_contao

KERN UX Integration für Contao CMS 5.7.

Dieses Bundle bindet das [KERN Plain-CSS-HTML-Kit](https://gitlab.opencode.de/kern-ux/kern-ux-plain) (Version 2.8.2, npm: `@kern-ux/native`) in Contao ein, stellt das Seitengerüst auf KERN um und passt die Twig-Templates der Inhaltselemente, Formulare und Navigation an das [KERN Designsystem](https://www.designsystem.gov.de/) an.

> **Status: Release Candidate.** Bitte vor dem Liveeinsatz auf einer Testinstallation prüfen und Probleme als [Issue](https://github.com/derkosmonaut/kern_contao/issues) melden.


## Voraussetzungen

- Contao 5.7 (Managed Edition), PHP >= 8.2
- Contao Manager **oder** Composer – SSH-Zugriff ist nicht nötig


## Installation

### Über den Contao Manager (empfohlen, ohne SSH)

1. Contao Manager → **Pakete** → nach `derkosmonaut/kern-contao` suchen und hinzufügen.
   Für den Release Candidate die Version explizit angeben (z. B. `^1.2@RC`), da der Manager sonst nur stabile Versionen vorschlägt.
2. **Änderungen anwenden**. Der Manager installiert das Paket und führt `assets:install` aus.
3. Im Contao-Backend unter **System → Systemwartung** bzw. über das Install-Tool die **Datenbank aktualisieren** (neue Felder in `tl_layout`, `tl_article`, `tl_content`).

### Über Composer

```bash
composer require derkosmonaut/kern-contao:^1.2@RC
vendor/bin/contao-console contao:migrate
```


## Einrichtung

1. **Layout → Seitenlayout** öffnen und im Bereich **KERN UX** **„KERN UX Standard verwenden“** aktivieren: lädt die KERN-Stylesheets und das Navigations-Script und stellt das Seitengerüst um (siehe unten).
2. Optional pro **Artikel**: **Container** auf „Volle Breite“ stellen (`kern-container-fluid` statt `kern-container`).
3. Optional pro Inhaltselement:
   - **Text** → **Alert-Variante** (Information, Erfolg, Warnung, Fehler) rendert den Text als `kern-alert`.
   - **Hyperlink** → **Button-Variante** (Primär, Sekundär, Tertiär) rendert den Link als `kern-btn`.

### Was die Layout-Checkbox umstellt

| Layouttyp | Mit Checkbox | Ohne Checkbox |
| --- | --- | --- |
| Klassisch (`fe_page`) | KERN-Assets, `kern-container` in Kopf- und Fußzeile, linke/rechte Spalte als `kern-row`/`kern-col-*`, `#main` ohne `.inside` | Core-Markup unverändert |
| Modern (`page/layout`) | KERN-Assets; die Slots gestaltet das eigene Layout-Template | Core-Markup unverändert |

**Wichtig:** Die Templates für Inhaltselemente, Artikel, Formulare und Navigation (Tabelle unten) gelten **installationsweit**, unabhängig von der Checkbox. Das Bundle ist für Installationen gedacht, die vollständig auf KERN setzen.


## Navigation

Das Modul **Navigationsmenü** (bzw. jedes Modul, das `mod_navigation`/`nav_default` nutzt) wird als barrierefreie Navigation nach dem [WAI-ARIA-Muster „Disclosure Navigation“](https://www.w3.org/WAI/ARIA/apg/patterns/disclosure/examples/disclosure-navigation/) ausgegeben. Das Designsystem hat dafür noch keine eigene Komponente; Optik und Abstände kommen aus den KERN-Tokens (`public/css/kern-contao.css`).

- **Ohne JavaScript** ist die Navigation eine vollständig ausgeklappte, verschachtelte Liste – alles bleibt erreichbar.
- **Mit JavaScript** (`public/js/kern-nav.js`, wird über die Layout-Checkbox geladen):
  - unter 992 px ein **Menü-Button** (`aria-expanded`, `aria-controls`), der die Navigation ein- und ausblendet; der Pfad zur aktuellen Seite ist beim Öffnen aufgeklappt,
  - ab 992 px eine horizontale Navigation mit **Dropdowns**,
  - Unterebenen über eigene **Buttons neben dem Link** – der Link selbst führt weiterhin zur Seite,
  - **Escape** schließt die offene Ebene und setzt den Fokus auf ihren Button zurück; Klick außerhalb oder Fokus außerhalb schließt Dropdowns.
- Kein `role="menu"`/`aria-haspopup`: Links bleiben Links, Screenreader lesen eine normale Liste vor.
- Der Skip-Link „Navigation überspringen“ wird bei Tastaturfokus sichtbar. Die Contao-Klasse `.invisible` ist als Screenreader-Klasse definiert (KERN bringt sie nicht mit).
- Der Breakpoint lässt sich über die CSS-Variable `--kern-nav-breakpoint` am `nav` ändern, z. B. `.kern-nav { --kern-nav-breakpoint: 1200px; }`.

Die Sitemap nutzt ebenfalls `nav_default`; sie wird ohne Buttons und vollständig ausgeklappt dargestellt.


## Hinweis zur Kopfzeile (Digitale Dachmarke)

Die KERN-Komponente **Kopfzeile** (Bundesflagge mit „Offizielle Website …“) ist Teil der [Digitalen Dachmarke](https://dachmarke.gov.de) und ausschließlich Angeboten von Bund, Ländern und Kommunen nach Beantragung vorbehalten. Sie steht nicht unter der Open-Source-Lizenz und wird von diesem Bundle deshalb **nicht** ausgegeben. Berechtigte Stellen können sie nach Freigabe in einem eigenen `fe_page`- bzw. Layout-Template ergänzen.


## Welche Templates auf KERN umgestellt werden

| Contao-Template | KERN-Markup |
| --- | --- |
| Überschrift (`component/_headline`) | `kern-heading-display` / `-x-large` / `-large` / `-medium` / `-small` |
| Text (`component/_rich_text`) | Wrapper `kern-content` (Token-Styles in `public/css/kern-contao.css`); optional `kern-alert` |
| Hyperlink | `kern-link` oder `kern-btn kern-btn--primary/secondary/tertiary` |
| Liste | `kern-list kern-list--bullet` bzw. `kern-list--number` |
| Tabelle | `kern-table` mit `__head/__body/__footer/__row/__header/__cell` |
| Download(s) | `kern-link` mit `kern-icon--download` |
| Akkordeon | natives `<details class="kern-accordion">` (ohne JavaScript, Handorgel entfällt) |
| Description List | `kern-description-list` |
| Teaser | `kern-card`, Link als `kern-btn kern-btn--secondary` |
| Toplink | `kern-link` mit `kern-icon--arrow-up` |
| Galerie | Liste im KERN-Grid (`kern-row`, `kern-col-12 kern-col-sm-6 kern-col-md-N`) |
| „Mehr lesen“ (`component/_read_more`) | `kern-link` mit `kern-icon--arrow-forward` |
| Artikel (`mod_article`) | innerer `kern-container` bzw. `kern-container-fluid` |
| Navigation (`mod_navigation`, `nav_default`) | barrierefreie Disclosure-Navigation mit Menü-Button (siehe unten) |
| Breadcrumb | `ol.kern-list.kern-list--horizontal` |
| Formulare (`form_*`) | `kern-form-input`, `kern-label`, `kern-hint`, `kern-error`, `kern-fieldset`, `kern-form-check`, `kern-btn` |

Die Komponenten- und Inhaltselement-Templates erweitern die Core-Templates (`{% use %}` bzw. `{% extends '@Contao/...' %}`) und überschreiben nur einzelne Blöcke. `mod_article` und die Formular-Templates sind vollständige Kopien der Core-Templates (Contao 5.7) und müssen bei Core-Updates abgeglichen werden.

Eigene Anpassungen nimmt man wie gewohnt über Templates im Ordner `templates/` der Installation vor; sie haben Vorrang vor denen des Bundles.


## Fehlerbehebung

### KERN-Stylesheets laden nicht (404 oder „MIME-Typ text/html / nosniff“)

Die Assets werden unter `public/bundles/kerncontao/` erwartet (Symfony streicht das Suffix „Bundle“). Fehlt die Datei, liefert der Webserver eine HTML-Fehlerseite aus – daher der nosniff-Hinweis im Browser.

1. Per FTP prüfen, ob `public/bundles/kerncontao` existiert und ob es ein (ggf. defekter) Symlink ist.
2. Funktionieren Symlinks grundsätzlich: Contao Manager → **Systemwartung** → **„Composer-Abhängigkeiten-Installer ausführen“** (führt `assets:install` erneut aus).
3. Verbietet der Hoster Symlinks (z. B. `Options -FollowSymLinks`): den Symlink löschen und den Inhalt von `vendor/derkosmonaut/kern-contao/public/` als echte Dateien nach `public/bundles/kerncontao/` hochladen. Das muss nach jedem Bundle-Update wiederholt werden. Ggf. hilft `Options +FollowSymLinks` in `public/.htaccess`.

Hinweis: Der Backend-Job „Symlinks neu erstellen“ legt nur `files`, `assets`, `themes` und `system` an, **nicht** `public/bundles/*`.

### Neue Felder fehlen im Backend

Nach jeder Installation oder jedem Update die Datenbank aktualisieren (Contao Manager → Datenbank-Migration bzw. `contao:migrate`) und den Cache leeren.


## KERN aktualisieren

Die Assets stammen aus dem npm-Paket `@kern-ux/native` (Release von [kern-ux-plain](https://gitlab.opencode.de/kern-ux/kern-ux-plain)):

```bash
npm pack @kern-ux/native@<VERSION>
tar xzf kern-ux-native-<VERSION>.tgz
cp package/dist/kern.min.css public/kern/kern.min.css
cp package/dist/fonts/fira-sans.css public/kern/fonts/
cp package/dist/fonts/fira-sans/*.woff2 public/kern/fonts/fira-sans/
cp package/LICENSE.md public/kern/LICENSE.md
```

Danach die Version in dieser README und im `CHANGELOG.md` anpassen. KERN-Klassenreferenz: [KERN UX Cheatsheet](https://digitalservicebund.github.io/kern-ux-cheatsheet/) und [kern-ux.de](https://www.kern-ux.de/komponenten/).


## Lizenz

- PHP-/Template-Code dieses Bundles: GPL-3.0-or-later (siehe `LICENSE`)
- Die eingebetteten KERN-Assets (`public/kern/`): EUPL-1.2 (siehe `public/kern/LICENSE.md`)
