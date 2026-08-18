<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('posts')
            ->where('type', 'publicatie')
            ->where(function ($query): void {
                $query->whereNull('button_text')
                    ->orWhere('button_text', '');
            })
            ->update([
                'button_text' => DB::raw('text'),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('posts')
            ->where('type', 'publicatie')
            ->update([
                'button_text' => null,
                'updated_at' => now(),
            ]);
    }
};