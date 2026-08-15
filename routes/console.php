<?php

use App\Models\QrLink;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('qr:clear', function () {
    DB::transaction(function (): void {
        QrLink::query()->delete();
    });

    Storage::disk('local')->deleteDirectory('qr-codes');

    $this->info('All QR codes, scan stats, and stored PNG files were removed.');
})->purpose('Delete all QR codes, stats, and stored QR images');

Artisan::command('admin:sync-user {--force : Force password update for existing admin user} {--user= : Explicit admin username override} {--pass= : Explicit admin password override} {--email= : Explicit admin email override}', function () {
    $adminUser = trim((string) ($this->option('user') ?: config('admin.user', '')));
    $adminPass = (string) ($this->option('pass') ?: config('admin.pass', ''));
    $adminEmail = trim((string) ($this->option('email') ?: config('admin.email', '')));

    if ($adminUser === '' || $adminPass === '') {
        $this->error('Missing admin values. Set ADMIN_USER/ADMIN_PASS or pass --user and --pass.');

        return 1;
    }

    if ($adminEmail === '') {
        $normalized = strtolower(preg_replace('/[^a-zA-Z0-9._-]/', '', $adminUser) ?: 'admin');
        $adminEmail = $normalized.'@fromthe.city';
    }

    $user = User::query()->where('email', $adminEmail)->first();

    if (! $user) {
        $user = User::query()->where('name', $adminUser)->first();
    }

    $isNew = ! $user;

    if (! $user) {
        $user = new User();
    }

    $user->name = $adminUser;
    $user->email = $adminEmail;

    if ($isNew || $this->option('force')) {
        $user->password = Hash::make($adminPass);
    }

    $user->email_verified_at = $user->email_verified_at ?: now();
    $user->save();

    $action = $isNew ? 'created' : 'updated';
    $this->info("Admin user {$action}: {$user->email}");

    if (! $isNew && ! $this->option('force')) {
        $this->line('Password was not changed. Use --force to reset it.');
    }

    return 0;
})->purpose('Create or sync admin user from config(admin.*)');
