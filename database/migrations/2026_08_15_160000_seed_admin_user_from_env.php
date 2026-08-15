<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $adminUser = trim((string) config('admin.user', ''));
        $adminPass = (string) config('admin.pass', '');

        if ($adminUser === '' || $adminPass === '') {
            return;
        }

        $adminEmail = trim((string) config('admin.email', ''));

        if ($adminEmail === '') {
            $normalized = strtolower(preg_replace('/[^a-zA-Z0-9._-]/', '', $adminUser) ?: 'admin');
            $adminEmail = $normalized.'@debijschijner.nl';
        }

        $user = User::query()->where('email', $adminEmail)->first();

        if (! $user) {
            $user = User::query()->where('name', $adminUser)->first();
        }

        if (! $user) {
            $user = new User();
        }

        $user->name = $adminUser;
        $user->email = $adminEmail;
        $user->password = Hash::make($adminPass);
        $user->email_verified_at = $user->email_verified_at ?: now();
        $user->save();
    }

    public function down(): void
    {
        $adminUser = trim((string) config('admin.user', ''));
        $adminEmail = trim((string) config('admin.email', ''));

        if ($adminEmail !== '') {
            User::query()->where('email', $adminEmail)->delete();

            return;
        }

        if ($adminUser !== '') {
            User::query()->where('name', $adminUser)->delete();
        }
    }
};
