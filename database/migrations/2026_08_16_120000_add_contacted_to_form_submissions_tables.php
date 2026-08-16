<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table): void {
            $table->boolean('contacted')->default(false)->after('user_agent');
        });

        Schema::table('apply_submissions', function (Blueprint $table): void {
            $table->boolean('contacted')->default(false)->after('user_agent');
        });
    }

    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table): void {
            $table->dropColumn('contacted');
        });

        Schema::table('apply_submissions', function (Blueprint $table): void {
            $table->dropColumn('contacted');
        });
    }
};
