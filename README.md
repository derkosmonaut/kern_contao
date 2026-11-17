# kern_contao

***ACHTUNG! Noch nicht für den Liveeinsatz verwenden!!!***

KERN UX Integration für Contao CMS 5.

Dieses Bundle bindet das [KERN Plain-CSS-HTML-Kit](https://gitlab.opencode.de/kern-ux/kern-ux-plain) (Version 2.8.2, npm: `@kern-ux/native`) in Contao ein und stellt die Inhalts-Twig-Templates von Contao auf KERN um.


## Voraussetzungen

- Contao 5.7 (Managed Edition), PHP >= 8.2
- Composer und SSH-Zugriff auf den Server (z. B. `kern.bbsmedien.de`)


Meldet der Browser zusätzlich „blockiert wegen MIME-Typ-Konflikt (text/html) / X-Content-Type-Options: nosniff“, antwortet der Webserver mit einer HTML-Fehlerseite, weil die Datei fehlt – sobald die Datei existiert, wird sie als `text/css` ausgeliefert und der nosniff-Fehler verschwindet.

**Ohne SSH:** `assets:install` legt `public/bundles/*` als Symlinks an (wird von `contao-setup` bzw. den post-install/update-Skripten mit `--symlink --relative` ausgeführt). Erlaubt der Hoster keine Symlinks (z. B. Apache `Options -FollowSymLinks`, „PHP symlink method is unavailable“), schlägt das fehl. Vorgehen dann:

1. Per FTP prüfen: existiert `public/bundles/kerncontaobundle`? Ist es ein (ggf. defekter) Symlink? Funktionieren die Symlinks der Core-Bundles (eine Datei unter `public/bundles/contaocorebundle/...` im Browser testen)?
2. Funktionieren Symlinks grundsätzlich, fehlt aber nur unser Eintrag: Contao Manager → Tab **Systemwartung** → **„Composer-Abhängigkeiten-Installer ausführen“** (führt die post-install-Skripte inkl. `assets:install` erneut aus) – oder das Paket im Manager erneut aktualisieren.
3. Sind Symlinks hostseitig verboten: per FTP den defekten Symlink `public/bundles/kerncontaobundle` löschen und den Inhalt von `vendor/derkosmonaut/kern-contao/public/` als echte Dateien nach `public/bundles/kerncontaobundle/` hochladen (`kern/kern.min.css`, `kern/fonts/…`, `css/kern-contao.css`). Das muss nach jedem Bundle-Update wiederholt werden. Abhilfe schafft ggf. `Options +FollowSymLinks` in `public/.htaccess` (falls AllowOverride das erlaubt) – dann legt `contao-setup` die Symlinks künftig selbst an.

Hinweis: Der Backend-Job „Symlinks neu erstellen“ (System → Systemwartung) legt nur files/assets/themes/system an, **nicht** `public/bundles/*` – für Bundle-Assets ist ausschließlich `assets:install` zuständig.

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
