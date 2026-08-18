<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mok_hits', function (Blueprint $table): void {
            $table->id();
            $table->string('path');
            $table->string('full_url', 2048);
            $table->string('method', 12)->nullable();
            $table->string('host')->nullable();
            $table->string('ip_address', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referer')->nullable();
            $table->string('accept_language')->nullable();
            $table->json('query_params')->nullable();
            $table->json('headers')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mok_hits');
    }
};