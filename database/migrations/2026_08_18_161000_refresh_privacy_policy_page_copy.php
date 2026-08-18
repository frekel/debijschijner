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

<p>Op deze pagina lees je op een heldere manier hoe De Bijschijner omgaat met persoonsgegevens. Ik vind het belangrijk dat jouw gegevens zorgvuldig worden behandeld en alleen worden gebruikt voor het doel waarvoor je ze aan mij geeft.</p>

<h4>Wie is verantwoordelijk voor deze website?</h4>
<p>De Bijschijner is verantwoordelijk voor de verwerking van persoonsgegevens via deze website.</p>
<p>
    Debora van der Stad<br>
    E-mail: <a href="mailto:debora@debijschijner.nl">debora@debijschijner.nl</a><br>
    KVK: 95218661
</p>

<h4>Welke gegevens verwerk ik?</h4>
<p>Als je contact opneemt of een aanvraag doet via de website, kan ik onder andere deze gegevens verwerken:</p>
<ul>
    <li>voornaam en achternaam;</li>
    <li>e-mailadres;</li>
    <li>telefoonnummer;</li>
    <li>de inhoud van je bericht of aanvraag;</li>
    <li>informatie over het gekozen traject;</li>
    <li>technische gegevens zoals IP-adres, browserinformatie en bezochte URL.</li>
</ul>

<h4>Waarvoor gebruik ik deze gegevens?</h4>
<p>Je gegevens worden gebruikt om:</p>
<ul>
    <li>contact met je op te nemen;</li>
    <li>een aanvraag te verwerken;</li>
    <li>mijn dienstverlening goed uit te voeren;</li>
    <li>de website veilig en technisch goed werkend te houden;</li>
    <li>campagnes en promopagina’s te meten, zoals QR-codes of advertentielinks.</li>
</ul>

<h4>Promopagina’s en campagneverkeer</h4>
<p>Sommige pagina’s op deze website zijn bedoeld voor campagnes, bijvoorbeeld voor een flyer, QR-code, radio- of tv-verwijzing. Als iemand zo’n pagina bezoekt, kan de website gegevens vastleggen zoals het pad, IP-adres, browserinformatie, referer, taalinstellingen en queryparameters. Dat helpt mij om te zien hoe een campagne gebruikt wordt en om technische misbruiksignalen te herkennen.</p>

<h4>Hoe lang bewaar ik gegevens?</h4>
<p>Ik bewaar persoonsgegevens niet langer dan nodig is voor het beantwoorden van vragen, het verwerken van aanvragen of het voeren van mijn administratie. Als er een wettelijke bewaarplicht geldt, houd ik mij daaraan.</p>

<h4>Deel ik gegevens met anderen?</h4>
<p>Ik verkoop jouw gegevens niet aan derden. Alleen als dat nodig is voor hosting, technische ondersteuning of een wettelijke verplichting, kunnen gegevens met een verwerker of instantie worden gedeeld.</p>

<h4>Cookies en technische informatie</h4>
<p>De website kan technische gegevens verwerken die nodig zijn voor de werking, beveiliging en prestaties van de site. Als er externe diensten worden gebruikt, gebeurt dat binnen de technische inrichting van deze website.</p>

<h4>Beveiliging</h4>
<p>Ik neem passende maatregelen om persoonsgegevens te beschermen tegen verlies, onbevoegde toegang of misbruik.</p>

<h4>Jouw rechten</h4>
<p>Je hebt het recht om jouw persoonsgegevens in te zien, te laten corrigeren of te laten verwijderen. Ook kun je bezwaar maken tegen een verwerking of vragen om beperking daarvan. Wil je hier gebruik van maken? Mail dan naar <a href="mailto:debora@debijschijner.nl">debora@debijschijner.nl</a>.</p>

<h4>Vragen?</h4>
<p>Heb je vragen over deze privacyverklaring of over hoe ik met jouw gegevens omga? Neem dan gerust contact op via <a href="mailto:debora@debijschijner.nl">debora@debijschijner.nl</a>.</p>
HTML,
                    'editor_mode' => 'html',
                ],
            ],
        ];

        DB::table('pages')
            ->where('slug', 'privacy-policy')
            ->update([
                'title' => 'Privacyverklaring',
                'content_blocks' => json_encode($contentBlocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'meta_title' => 'Privacyverklaring',
                'meta_description' => 'Lees op een heldere manier hoe De Bijschijner omgaat met persoonsgegevens, contactaanvragen en campagneverkeer.',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
    }
};