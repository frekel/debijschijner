<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->unsignedInteger('sort_order')->default(0)->after('canonical_url');
        });

        $pages = DB::table('pages')
            ->select('id')
            ->orderBy('id')
            ->get();

        foreach ($pages as $index => $page) {
            DB::table('pages')
                ->where('id', $page->id)
                ->update(['sort_order' => $index + 1]);
        }
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->dropColumn('sort_order');
        });
    }
};