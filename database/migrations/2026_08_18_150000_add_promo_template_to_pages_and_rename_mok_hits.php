<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->string('promo_redirect_url')->nullable()->after('form_title');
        });

        if (Schema::hasTable('mok_hits') && ! Schema::hasTable('promo_hits')) {
            Schema::rename('mok_hits', 'promo_hits');
        }

        Schema::table('promo_hits', function (Blueprint $table): void {
            $table->foreignId('page_id')->nullable()->after('id')->constrained('pages')->nullOnDelete();
            $table->string('page_slug')->nullable()->after('page_id');
            $table->string('page_title')->nullable()->after('page_slug');
            $table->string('redirect_target', 2048)->nullable()->after('full_url');
        });
    }

    public function down(): void
    {
        Schema::table('promo_hits', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('page_id');
            $table->dropColumn(['page_slug', 'page_title', 'redirect_target']);
        });

        if (Schema::hasTable('promo_hits') && ! Schema::hasTable('mok_hits')) {
            Schema::rename('promo_hits', 'mok_hits');
        }

        Schema::table('pages', function (Blueprint $table): void {
            $table->dropColumn('promo_redirect_url');
        });
    }
};