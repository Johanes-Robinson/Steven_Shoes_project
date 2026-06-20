<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'customer.dashboard');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = $request->user();
        $user->syncRoleWithEmail();

        if (! $user->hasVerifiedEmail()) {
            Auth::logout();
            $request->session()->regenerateToken();

            return redirect()
                ->route('verification.notice')
                ->with('email', $user->email)
                ->with('verification_type', 'account')
                ->with('status', 'Silakan verifikasi email terlebih dahulu sebelum masuk.');
        }

        return redirect()->intended(
            $user->isAdmin()
                ? route('admin.dashboard')
                : route('customer.dashboard')
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
