<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pages')
            ->where('slug', 'contact')
            ->update([
                'template' => 'form',
                'form_title' => 'Neem contact op',
                'content_blocks' => json_encode([
                    ['type' => 'contact_form', 'data' => []],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);

        DB::table('pages')
            ->where('slug', 'aanvraag')
            ->update([
                'template' => 'form',
                'form_title' => 'Vraag een traject aan',
                'content_blocks' => json_encode([
                    ['type' => 'apply_form', 'data' => []],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('pages')
            ->whereIn('slug', ['contact', 'aanvraag'])
            ->update([
                'content_blocks' => json_encode([], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
    }
};