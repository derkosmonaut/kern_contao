# Changelog

Alle nennenswerten Änderungen an diesem Bundle. Das Format orientiert sich an [Keep a Changelog](https://keepachangelog.com/de/1.1.0/), die Versionierung an [Semantic Versioning](https://semver.org/lang/de/).

## [1.2.0-RC1]

### Hinzugefügt
- KERN-Seitengerüst für das klassische Layout (`fe_page`): `kern-container` in Kopf- und Fußzeile, Spalten als `kern-row`/`kern-col-*`.
- Unterstützung für das moderne Seitenlayout (`page/layout`): KERN-Assets und Kopfzeile.
- Seitenlayout-Feld „KERN-Kopfzeile“ für den Kopfzeilentext; die Kopfzeile zeigt jetzt die Bundesflagge.
- Artikel-Feld „Container“ (`kern-container` / `kern-container-fluid`).
- Navigation, Breadcrumb und Artikel auf KERN umgestellt.
- `CHANGELOG.md`, Composer-Metadaten, CI mit Twig-Syntaxprüfung (`tests/lint-twig.php`).

### Geändert
- Seitengerüst und Assets greifen nur noch, wenn im Seitenlayout „KERN UX Standard verwenden“ aktiv ist; sonst bleibt das Core-Markup unverändert.
- KERN-Stylesheets werden nach den Meta-Tags und vor den Theme-Stylesheets eingebunden.
- Die Kopfzeile wird nur ausgegeben, wenn ein Kopfzeilentext gepflegt ist (vorher: Seitentitel).
- Galerie als Liste (`ul`/`li`) im KERN-Grid, wie im Core.
- Tabellenköpfe erhalten `scope="col"`.

### Behoben
- `mod_article` definierte den Block `content` doppelt – Twig bricht dabei mit einem Syntaxfehler ab (Seiten mit Artikeln liefen auf einen Fehler). Wrapper wieder `<div>` wie im Core 5.7.
- Artikel-Option „Volle Breite“ hatte keine Wirkung (Feld wurde über `data.*` statt direkt gelesen).
- Insert-Tags im Label des Absende-Buttons und im `aria-label` der Navigation wurden nicht ersetzt.
- Passwortfelder verloren die Klasse `password`; Formular-Bestätigung behält die Core-Klasse `form-confirmation`.
- README nannte den falschen Asset-Pfad (`bundles/kerncontaobundle` statt `bundles/kerncontao`).

### Entfernt
- `kern-kopfzeile.js` (Web Component wurde nicht verwendet; die Kopfzeile nutzt die Plain-CSS-Variante).

## [1.1.0]
- KERN-Assets nur im Frontend, Seitenlayout-Checkbox, Button- und Alert-Varianten, Formular-Templates.

## [1.0.0]
- Erste Version: KERN-Assets und KERN-Inhaltselement-Templates für Contao 5.7.
