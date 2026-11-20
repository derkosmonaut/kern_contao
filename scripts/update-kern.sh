#!/usr/bin/env bash
#
# KERN-Assets aus dem npm-Paket @kern-ux/native (kern-ux-plain) übernehmen.
#
#   scripts/update-kern.sh              neueste Version
#   scripts/update-kern.sh 2.9.0        bestimmte Version
#   scripts/update-kern.sh --from DIR   bereits entpacktes Paket (Ordner mit package.json)
#
# Übernimmt kern.min.css und LICENSE.md unverändert. Von Fira Sans werden nur
# die Schnitte kopiert, die KERN und Fließtext nutzen (400–700, jeweils normal
# und kursiv); fira-sans.css wird dafür neu erzeugt (mit font-display: swap).
# Die Version landet in public/kern/VERSION.

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
TARGET="$ROOT/public/kern"
PACKAGE="@kern-ux/native"

# Schnitt-Datei => "Gewicht Stil"
FONTS=(
    "FiraSans-Regular:400:normal"
    "FiraSans-Italic:400:italic"
    "FiraSans-Medium:500:normal"
    "FiraSans-MediumItalic:500:italic"
    "FiraSans-SemiBold:600:normal"
    "FiraSans-SemiBoldItalic:600:italic"
    "FiraSans-Bold:700:normal"
    "FiraSans-BoldItalic:700:italic"
)

WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

if [[ "${1:-}" == "--from" ]]; then
    SRC="$(cd "${2:?Ordner fehlt}" && pwd)"
else
    VERSION="${1:-latest}"
    (cd "$WORK" && npm pack --silent "$PACKAGE@$VERSION" >/dev/null)
    tar -xzf "$WORK"/*.tgz -C "$WORK"
    SRC="$WORK/package"
fi

VERSION="$(node -p "require('$SRC/package.json').version")"

for f in dist/kern.min.css LICENSE.md; do
    [[ -f "$SRC/$f" ]] || { echo "Fehlt im Paket: $f" >&2; exit 1; }
done

FONT_SRC="$SRC/dist/fonts/fira-sans"
for entry in "${FONTS[@]}"; do
    [[ -f "$FONT_SRC/${entry%%:*}.woff2" ]] || { echo "Fehlt im Paket: dist/fonts/fira-sans/${entry%%:*}.woff2" >&2; exit 1; }
done

cp "$SRC/dist/kern.min.css" "$TARGET/kern.min.css"
cp "$SRC/LICENSE.md" "$TARGET/LICENSE.md"

rm -rf "$TARGET/fonts/fira-sans"
mkdir -p "$TARGET/fonts/fira-sans"

{
    echo "/* Fira Sans – Auszug aus $PACKAGE $VERSION (erzeugt von scripts/update-kern.sh) */"
    for entry in "${FONTS[@]}"; do
        IFS=: read -r file weight style <<< "$entry"
        cp "$FONT_SRC/$file.woff2" "$TARGET/fonts/fira-sans/"
        cat <<EOF

@font-face {
    font-family: 'Fira Sans';
    src: url('./fira-sans/$file.woff2') format('woff2');
    font-weight: $weight;
    font-style: $style;
    font-display: swap;
}
EOF
    done
} > "$TARGET/fonts/fira-sans.css"

echo "$VERSION" > "$TARGET/VERSION"
echo "KERN $VERSION übernommen."
