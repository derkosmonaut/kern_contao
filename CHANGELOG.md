# Changelog

Alle nennenswerten Änderungen an diesem Bundle. Das Format orientiert sich an [Keep a Changelog](https://keepachangelog.com/de/1.1.0/), die Versionierung an [Semantic Versioning](https://semver.org/lang/de/).

## [1.3.0-RC1]

### Hinzugefügt
- Backend-Button **„KERN-Grundeinrichtung“** (Layout → Themes): Theme, Seitenlayout, Module und – auf leeren Installationen – Seitenbaum mit Startseite, Kontakt, Leichte Sprache, Gebärdensprache, Impressum, Datenschutz, Erklärung zur Barrierefreiheit, Suche und 404-Seite; Formular „Barriere melden“.
- Frontend-Modul **„Darstellung anpassen“**: Schrift (Fira Sans / Atkinson Hyperlegible Next), Farbschema (Gerät/hell/dunkel), Textabstände; Speicherung nur im Browser.
- Frontend-Modul **„Leichte Sprache / Gebärdensprache“** mit KERN-Icons.
- Sprunglink „Zum Inhalt springen“ in beiden Layouttypen.
- Modernes Layout: Slots `header`/`main`/`footer` mit KERN-Containern.
- Styles für Kopf- und Fußbereich, Druckansicht.
- `scripts/update-kern.sh` und wöchentliche GitHub Action „KERN-Update“, die bei neuen KERN-Versionen einen Pull Request öffnet; `public/kern/VERSION`.
- Schrift Atkinson Hyperlegible Next (SIL OFL 1.1) als Variable Font.

### Geändert
- Fira Sans nur noch in den genutzten Schnitten (400–700, normal/kursiv) mit `font-display: swap`: 4,3 MB → 1,1 MB.
- `body` ohne Standardrand; `color-scheme` passend zum Farbschema.
- Twig-Lint kennt Contaos `slot`- und `defer`-Tags.

## [1.2.0-RC1]

### Hinzugefügt
- KERN-Seitengerüst für das klassische Layout (`fe_page`): `kern-container` in Kopf- und Fußzeile, Spalten als `kern-row`/`kern-col-*`.
- Unterstützung für das moderne Seitenlayout (`page/layout`): KERN-Assets.
- Barrierefreie Navigation (Disclosure-Muster nach WAI-ARIA APG) mit Menü-Button auf kleinen Bildschirmen, Dropdowns auf großen, Escape-/Fokus-Handling, ohne JavaScript voll ausgeklappt (`js/kern-nav.js`).
- Contao-Klasse `.invisible` als Screenreader-Klasse; Skip-Link bei Tastaturfokus sichtbar.
- Artikel-Feld „Container“ (`kern-container` / `kern-container-fluid`).
- Breadcrumb und Artikel auf KERN umgestellt.
- `CHANGELOG.md`, Composer-Metadaten, CI mit Twig-Syntaxprüfung (`tests/lint-twig.php`).

### Geändert
- Seitengerüst und Assets greifen nur noch, wenn im Seitenlayout „KERN UX Standard verwenden“ aktiv ist; sonst bleibt das Core-Markup unverändert.
- KERN-Stylesheets werden nach den Meta-Tags und vor den Theme-Stylesheets eingebunden.
- Galerie als Liste (`ul`/`li`) im KERN-Grid, wie im Core.
- Tabellenköpfe erhalten `scope="col"`.

### Behoben
- `mod_article` definierte den Block `content` doppelt – Twig bricht dabei mit einem Syntaxfehler ab (Seiten mit Artikeln liefen auf einen Fehler). Wrapper wieder `<div>` wie im Core 5.7.
- Artikel-Option „Volle Breite“ hatte keine Wirkung (Feld wurde über `data.*` statt direkt gelesen).
- Insert-Tags im Label des Absende-Buttons und im `aria-label` der Navigation wurden nicht ersetzt.
- Passwortfelder verloren die Klasse `password`; Formular-Bestätigung behält die Core-Klasse `form-confirmation`.
- README nannte den falschen Asset-Pfad (`bundles/kerncontaobundle` statt `bundles/kerncontao`).

### Entfernt
- KERN-Kopfzeile samt `kern-kopfzeile.js`: Sie gehört zur Digitalen Dachmarke und ist Bund, Ländern und Kommunen vorbehalten (nicht Open Source).
- `aria-haspopup` an Navigationslinks (die Navigation ist kein Menü im Sinne von ARIA).

## [1.1.0]
- KERN-Assets nur im Frontend, Seitenlayout-Checkbox, Button- und Alert-Varianten, Formular-Templates.

## [1.0.0]
- Erste Version: KERN-Assets und KERN-Inhaltselement-Templates für Contao 5.7.
