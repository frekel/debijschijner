<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pages')
            ->where('slug', 'over-mij/publicaties')
            ->update([
                'content_blocks' => json_encode([
                    ['type' => 'publications', 'data' => []],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('pages')
            ->where('slug', 'over-mij/publicaties')
            ->update([
                'content_blocks' => json_encode([], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
    }
};