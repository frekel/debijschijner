<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pages')
            ->where('slug', 'publicaties')
            ->update([
                'slug' => 'over-mij/publicaties',
                'canonical_url' => '/over-mij/publicaties/',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('pages')
            ->where('slug', 'over-mij/publicaties')
            ->update([
                'slug' => 'publicaties',
                'canonical_url' => '/publicaties/',
                'updated_at' => now(),
            ]);
    }
};