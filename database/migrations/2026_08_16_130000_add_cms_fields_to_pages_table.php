<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->json('content_blocks')->nullable()->after('html');
            $table->string('meta_title', 70)->nullable()->after('content_blocks');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('og_image')->nullable()->after('meta_description');
            $table->string('canonical_url')->nullable()->after('og_image');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->dropColumn([
                'content_blocks',
                'meta_title',
                'meta_description',
                'og_image',
                'canonical_url',
            ]);
        });
    }
};
