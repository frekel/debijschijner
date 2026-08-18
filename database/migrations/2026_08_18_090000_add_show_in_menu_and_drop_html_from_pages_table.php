<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->boolean('show_in_menu')->default(true)->after('is_published');
            $table->dropColumn('html');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->longText('html')->nullable()->after('title');
            $table->dropColumn('show_in_menu');
        });
    }
};