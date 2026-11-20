<?php

declare(strict_types=1);

namespace Derkosmonaut\KernContaoBundle\Setup;

/**
 * Seitenbaum und Platzhaltertexte der KERN-Grundeinrichtung.
 *
 * Alle Stellen in [eckigen Klammern] müssen vor der Veröffentlichung angepasst
 * werden. Die Texte sind Vorlagen und keine Rechtsberatung.
 */
final class KernSetupContent
{
    private const TEMPLATE_NOTE = '<p>Diese Seite wurde von der KERN-Grundeinrichtung angelegt. Bitte alle Angaben in [eckigen Klammern] ersetzen und den Text rechtlich prüfen lassen, bevor die Website veröffentlicht wird.</p>';

    /**
     * @return array<string, array{title: string, alias: string, type?: string, hide?: bool, robots?: string, elements: list<array<string, string>>}>
     */
    public function pages(string $siteName): array
    {
        $note = ['type' => 'text', 'headline' => 'Vorlage', 'alert' => 'warning', 'text' => self::TEMPLATE_NOTE];

        return [
            'start' => [
                'title' => 'Startseite',
                'alias' => 'index',
                'elements' => [
                    ['headline' => 'Willkommen bei '.$siteName, 'unit' => 'h1', 'text' => '<p>[Kurze Einleitung: Wer sind Sie, was finden Besucherinnen und Besucher auf dieser Website?]</p>'],
                ],
            ],
            'kontakt' => [
                'title' => 'Kontakt',
                'alias' => 'kontakt',
                'elements' => [
                    ['headline' => 'Kontakt', 'unit' => 'h1', 'text' => '<p>[Name der Stelle]<br>[Straße Hausnummer]<br>[PLZ Ort]</p><p>Telefon: [Telefonnummer]<br>E-Mail: [E-Mail-Adresse]</p><p>[Öffnungs- bzw. Sprechzeiten]</p>'],
                ],
            ],
            'leichte_sprache' => [
                'title' => 'Leichte Sprache',
                'alias' => 'leichte-sprache',
                'hide' => true,
                'elements' => [
                    $note,
                    ['headline' => 'Informationen in Leichter Sprache', 'unit' => 'h1', 'text' => '<p>[Hier steht in Leichter Sprache, was es auf dieser Website gibt.]</p><p>[Wer wir sind.]</p><p>[Was wir machen.]</p><p>[Wie man uns erreicht.]</p>'],
                ],
            ],
            'gebaerdensprache' => [
                'title' => 'Gebärdensprache',
                'alias' => 'gebaerdensprache',
                'hide' => true,
                'elements' => [
                    $note,
                    ['headline' => 'Informationen in Deutscher Gebärdensprache', 'unit' => 'h1', 'text' => '<p>[Hier Videos in Deutscher Gebärdensprache einbinden – mit Untertiteln bzw. Textfassung. Inhalt: was es auf dieser Website gibt, wer wir sind, wie man uns erreicht.]</p>'],
                ],
            ],
            'impressum' => [
                'title' => 'Impressum',
                'alias' => 'impressum',
                'hide' => true,
                'elements' => [
                    $note,
                    ['headline' => 'Impressum', 'unit' => 'h1', 'text' => '<p>[Name der Stelle]<br>[Straße Hausnummer]<br>[PLZ Ort]</p><p>Vertreten durch: [Name, Funktion]</p><p>Telefon: [Telefonnummer]<br>E-Mail: [E-Mail-Adresse]</p><p>[Aufsichtsbehörde, ggf. Umsatzsteuer-ID]</p><p>Verantwortlich für den Inhalt: [Name, Anschrift]</p>'],
                ],
            ],
            'datenschutz' => [
                'title' => 'Datenschutz',
                'alias' => 'datenschutz',
                'hide' => true,
                'elements' => [
                    $note,
                    ['headline' => 'Datenschutzerklärung', 'unit' => 'h1', 'text' => '<p>[Datenschutzerklärung einfügen: Verantwortliche Stelle, Datenschutzbeauftragte bzw. Datenschutzbeauftragter, Zwecke und Rechtsgrundlagen der Verarbeitung, Server-Logfiles, Kontaktformulare inklusive „Barriere melden“, Speicherdauer, Rechte der betroffenen Personen, Beschwerderecht bei der Aufsichtsbehörde.]</p><p>Hinweis zur Funktion „Darstellung anpassen“: Ihre Auswahl (Schrift, Farbschema, Textabstände) wird nur in Ihrem Browser gespeichert (localStorage) und nicht an uns übertragen.</p>'],
                ],
            ],
            'barrierefreiheit' => [
                'title' => 'Erklärung zur Barrierefreiheit',
                'alias' => 'barrierefreiheit',
                'hide' => true,
                'elements' => [
                    $note,
                    ['headline' => 'Erklärung zur Barrierefreiheit', 'unit' => 'h1', 'text' => '<p>[Name der Stelle] ist bemüht, die Website [Adresse der Website] im Einklang mit den Vorschriften zur Barrierefreiheit barrierefrei zugänglich zu machen. [Rechtsgrundlage, z. B. Behindertengleichstellungsgesetz (BGG) und Barrierefreie-Informationstechnik-Verordnung (BITV 2.0) bzw. das entsprechende Landesgesetz.]</p>'],
                    ['headline' => 'Stand der Vereinbarkeit mit den Anforderungen', 'text' => '<p>Diese Website ist [vollständig / teilweise / nicht] mit den Anforderungen [Rechtsgrundlage] vereinbar.</p>'],
                    ['headline' => 'Nicht barrierefreie Inhalte', 'text' => '<p>[Inhalte auflisten, die nicht barrierefrei sind, jeweils mit Begründung (z. B. unverhältnismäßige Belastung) und – wenn vorhanden – barrierefreien Alternativen. Falls alles barrierefrei ist: Abschnitt entfernen.]</p>'],
                    ['headline' => 'Erstellung dieser Erklärung', 'text' => '<p>Diese Erklärung wurde am [Datum] erstellt und zuletzt am [Datum] überprüft. Grundlage ist [eine Selbstbewertung / ein Test durch Dritte, z. B. BITV-Test].</p>'],
                    ['headline' => 'Barriere melden: Kontakt zu den Feedback-Kontaktstellen', 'text' => '<p>Sind Ihnen Mängel beim barrierefreien Zugang zu Inhalten dieser Website aufgefallen? Oder haben Sie Fragen zur Barrierefreiheit? Dann können Sie sich gerne an uns wenden – über das folgende Formular oder direkt:</p><p>[Name der Kontaktstelle]<br>E-Mail: [E-Mail-Adresse]<br>Telefon: [Telefonnummer]</p>'],
                    ['type' => 'form'],
                    ['headline' => 'Schlichtungsverfahren', 'text' => '<p>Wenn auch nach Ihrem Feedback an die oben genannte Kontaktstelle keine zufriedenstellende Lösung gefunden wurde, können Sie sich an die [zuständige Schlichtungsstelle] wenden. [Für Stellen des Bundes: Schlichtungsstelle nach dem Behindertengleichstellungsgesetz, www.schlichtungsstelle-bgg.de. Für Stellen der Länder und Kommunen: Schlichtungs- bzw. Durchsetzungsstelle des jeweiligen Bundeslandes eintragen.]</p><p>Das Schlichtungsverfahren ist kostenlos. Sie brauchen auch keinen Rechtsbeistand.</p>'],
                ],
            ],
            'suche' => [
                'title' => 'Suche',
                'alias' => 'suche',
                'hide' => true,
                'robots' => 'noindex,follow',
                'elements' => [
                    ['type' => 'headline', 'headline' => 'Suche', 'unit' => 'h1'],
                    ['type' => 'module'],
                ],
            ],
            'nicht_gefunden' => [
                'title' => 'Seite nicht gefunden',
                'alias' => 'seite-nicht-gefunden',
                'type' => 'error_404',
                'hide' => true,
                'robots' => 'noindex,nofollow',
                'elements' => [
                    ['headline' => 'Seite nicht gefunden', 'unit' => 'h1', 'text' => '<p>Die aufgerufene Seite gibt es nicht oder nicht mehr. Vielleicht hilft Ihnen die <a href="{{link_url::suche}}">Suche</a> oder die <a href="{{env::path}}">Startseite</a> weiter.</p>'],
                ],
            ],
        ];
    }
}
