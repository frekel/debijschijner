<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $contentBlocks = [
            [
                'type' => 'rich_text',
                'data' => [
                    'heading' => 'Privacyverklaring',
                    'body_html' => <<<'HTML'
<p>Laatst bijgewerkt: 18 augustus 2026</p>

<p>Via deze privacyverklaring informeer ik je over de manier waarop persoonsgegevens worden verwerkt via deze website van De Bijschijner.</p>

<h4>1. Wie is verantwoordelijk?</h4>
<p>De Bijschijner<br>
Debora van der Stad<br>
E-mail: <a href="mailto:debora@debijschijner.nl">debora@debijschijner.nl</a></p>

<h4>2. Welke persoonsgegevens verwerk ik?</h4>
<p>Wanneer je contact opneemt of een aanvraag doet via deze website, kan ik de volgende gegevens verwerken:</p>
<ul>
    <li>voornaam en achternaam;</li>
    <li>e-mailadres;</li>
    <li>telefoonnummer;</li>
    <li>inhoud van je bericht of aanvraag;</li>
    <li>gegevens over je gekozen traject;</li>
    <li>IP-adres en browserinformatie voor beveiliging en technische logging.</li>
 </ul>

<h4>3. Waarvoor gebruik ik deze gegevens?</h4>
<p>Je gegevens worden uitsluitend gebruikt voor:</p>
<ul>
    <li>het beantwoorden van je bericht;</li>
    <li>het verwerken van je aanvraag;</li>
    <li>het verbeteren van de veiligheid en werking van de website;</li>
    <li>het meten van promotionele pagina’s, zoals QR- of campagnepagina’s.</li>
</ul>

<h4>4. Promo- en campagnepagina’s</h4>
<p>Voor speciale campagnepagina’s, zoals QR-links of advertentielinks, kan de website een bezoek registreren. Daarbij kunnen onder meer het bezochte pad, IP-adres, browserinformatie, referer, taalinstellingen en queryparameters worden opgeslagen. Deze gegevens worden alleen gebruikt om het bereik en gebruik van campagnes te analyseren en de website technisch te beveiligen.</p>

<h4>5. Hoe lang bewaar ik je gegevens?</h4>
<p>Ik bewaar persoonsgegevens niet langer dan noodzakelijk is voor het doel waarvoor ze zijn verzameld, tenzij ik wettelijk verplicht ben gegevens langer te bewaren.</p>

<h4>6. Delen met derden</h4>
<p>Je persoonsgegevens worden niet verkocht aan derden. Gegevens worden alleen gedeeld als dat nodig is voor de uitvoering van de dienstverlening, voor hosting en technische ondersteuning, of wanneer daar een wettelijke verplichting voor bestaat.</p>

<h4>7. Cookies en technische gegevens</h4>
<p>De website kan technische gegevens verwerken die nodig zijn voor het functioneren van de site, beveiliging en statistieken. Als er analytische of andere externe diensten worden gebruikt, gebeurt dat binnen de instellingen en technische inrichting van deze website.</p>

<h4>8. Beveiliging</h4>
<p>Ik neem passende technische en organisatorische maatregelen om persoonsgegevens te beschermen tegen verlies, misbruik of onbevoegde toegang.</p>

<h4>9. Jouw rechten</h4>
<p>Je hebt het recht om je persoonsgegevens in te zien, te corrigeren of te laten verwijderen. Ook kun je bezwaar maken tegen de verwerking of verzoeken om beperking daarvan. Voor zulke verzoeken kun je contact opnemen via <a href="mailto:debora@debijschijner.nl">debora@debijschijner.nl</a>.</p>

<h4>10. Vragen</h4>
<p>Heb je vragen over deze privacyverklaring of over de verwerking van je persoonsgegevens? Neem dan contact op via <a href="mailto:debora@debijschijner.nl">debora@debijschijner.nl</a>.</p>
HTML,
                    'editor_mode' => 'html',
                ],
            ],
        ];

        $existing = DB::table('pages')->where('slug', 'privacy-policy')->first();

        $payload = [
            'title' => 'Privacyverklaring',
            'template' => 'default',
            'content_blocks' => json_encode($contentBlocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'meta_title' => 'Privacyverklaring',
            'meta_description' => 'Lees hoe De Bijschijner omgaat met persoonsgegevens, contactaanvragen en campagnebezoeken.',
            'canonical_url' => '/privacy-policy/',
            'is_published' => true,
            'show_in_menu' => false,
            'updated_at' => now(),
        ];

        if ($existing) {
            DB::table('pages')->where('slug', 'privacy-policy')->update($payload);

            return;
        }

        DB::table('pages')->insert($payload + [
            'slug' => 'privacy-policy',
            'sort_order' => 999,
            'created_at' => now(),
        ]);
    }

    public function down(): void
    {
    }
};