<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->dropColumn([
                'form_email_text',
                'form_phone_text',
                'form_address_text',
                'form_extra_text',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->text('form_email_text')->nullable()->after('form_title');
            $table->text('form_phone_text')->nullable()->after('form_email_text');
            $table->text('form_address_text')->nullable()->after('form_phone_text');
            $table->text('form_extra_text')->nullable()->after('form_address_text');
        });
    }
};