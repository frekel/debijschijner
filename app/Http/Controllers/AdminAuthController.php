<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLoginForm(Request $request): RedirectResponse|View
    {
        if ($request->session()->get('is_admin', false)) {
            return redirect()->route('admin.pages.index');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = trim($credentials['username']);
        $password = $credentials['password'];

        $user = User::query()
            ->where('name', $username)
            ->orWhere('email', $username)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return back()
                ->withErrors(['username' => 'Invalid credentials.'])
                ->withInput($request->only('username'));
        }

        $request->session()->regenerate();
        Auth::login($user);
        $request->session()->put('is_admin', true);
        $request->session()->put('admin_user', $user->name);

        return redirect()->route('admin.pages.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->forget(['is_admin', 'admin_user']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
