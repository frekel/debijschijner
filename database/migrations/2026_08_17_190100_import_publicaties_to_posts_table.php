<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $publications = [
            [
                'slug' => '9-tips-voor-de-gang-naar-de-brugklas',
                'title' => '9 tips voor de gang naar de brugklas',
                'image' => '/images/uploads/2024/09/PB39_05_beeld_negen_tips-324x160-1-e1725818747354.jpg',
                'text' => 'Sommige kinderen kijken enorm naar uit, maar voor anderen is het best spannend: hoe bereid je de kinderen uit je groep v…',
            ],
            [
                'slug' => 'uit-de-praktijk-wat-een-jaar',
                'title' => 'Uit de praktijk: Wat een jaar!',
                'image' => '/images/uploads/2024/09/PB36_09_beeld_Uit-de-praktijk_Grej-of-the-day-696x343-1.png',
                'text' => 'Hopelijk verdwaal je niet steeds op zoek naar het goede lokaal en maak je snel vrienden.’ Zomaar een fragment van een gr…',
            ],
            [
                'slug' => 'uit-de-praktijk-verjaardagen-vieren',
                'title' => 'Uit de praktijk: Verjaardagen vieren',
                'image' => '/images/uploads/2024/09/PB36_07_beeld_Uit-de-praktijk_Klassenapp-696x343-1.png',
                'text' => 'Voor een jarige job is het vaak het hoogtepunt van de viering: alle klassen rondgaan. Maar voor de leerkrachten is het o…',
            ],
            [
                'slug' => 'uit-de-praktijk-woordsoorten-benoemen',
                'title' => 'Uit de praktijk: Woordsoorten benoemen',
                'image' => '/images/uploads/2024/09/PB36_05_beeld_Uit-de-praktijk_Kindgesprekken-696x343-1.png',
                'text' => 'Weinig kinderen zullen razend enthousiast aan de slag gaan met een opdracht als ‘onderstreep alle werkwoorden, bijvoegli…',
            ],
            [
                'slug' => 'uit-de-praktijk-de-klassenvergadering',
                'title' => 'Uit de praktijk: De klassenvergadering',
                'image' => '/images/uploads/2024/09/PB36_04_beeld_Uit-de-praktijk_De-boekendoos-696x343-1.png',
                'text' => 'Voor ‘grote mensen’ is vergaderen heel gewoon, maar in de groep van auteur Debora van der Stad zijn de kinderen intussen…',
            ],
            [
                'slug' => 'uit-de-praktijk-het-vriendschapssociogram',
                'title' => 'Uit de praktijk: Het vriendschapssociogram',
                'image' => '/images/uploads/2024/09/PB36_01_beeld_Uit-de-praktijk-696x343-1.png',
                'text' => 'Hoe krijg je inzicht in de sociale structuur van je groep? De auteur van dit artikel stelt een vriendschapssociogram op …',
            ],
            [
                'slug' => 'interview-juf-meester',
                'title' => 'Interview Juf & Meester',
                'image' => '/images/uploads/2024/09/Screenshot-from-2024-09-09-08-18-40.png',
                'text' => 'De leerkracht leert doorDebora van der Stad doet de tweejarige post-hbo-opleiding totbeeldcoach aan Hogeschool InHolland…',
            ],
            [
                'slug' => 'interview-malmberg-onderwijs-methode-verkeer',
                'title' => 'Interview Malmberg: Onderwijs methode “VERKEER”',
                'image' => '/images/uploads/2024/10/De-Bijschijner-e1730121170702-300x131.jpg',
                'text' => 'https://youtu.be/ksugJO_Jrog\n\n\n\n\nBekijk artikel op Malmberg.nl…',
            ],
        ];

        $sortOrder = (int) DB::table('posts')->max('sort_order');

        foreach ($publications as $publication) {
            $existing = DB::table('posts')
                ->where('type', 'publicatie')
                ->where('slug', $publication['slug'])
                ->first();

            $payload = [
                'type' => 'publicatie',
                'slug' => $publication['slug'],
                'title' => $publication['title'],
                'text' => $publication['text'] !== '' ? '<p>'.$publication['text'].'</p>' : null,
                'editor_mode' => 'html',
                'image' => $publication['image'] !== '' ? $publication['image'] : null,
                'is_published' => true,
                'updated_at' => now(),
            ];

            if ($existing) {
                DB::table('posts')
                    ->where('id', $existing->id)
                    ->update($payload);

                continue;
            }

            $sortOrder++;

            DB::table('posts')->insert($payload + [
                'sort_order' => $sortOrder,
                'created_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('posts')
            ->where('type', 'publicatie')
            ->delete();
    }
};