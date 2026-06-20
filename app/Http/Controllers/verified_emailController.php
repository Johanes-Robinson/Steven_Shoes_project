<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class verified_emailController extends Controller
{
    public function show()
    {
        return view('verified_email', [
            'email' => session('email'),
            'type' => session('verification_type', 'account'),
        ]);
    }

    public function verify(Request $request, string $id, string $hash)
    {
        $user = User::findOrFail($id);

        abort_unless(hash_equals((string) $hash, sha1($user->getEmailForVerification())), 403);

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return redirect()
            ->route('login')
            ->with('status', 'Email berhasil diverifikasi. Silakan masuk untuk melanjutkan.');
    }

    public function resendVerification(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $data['email'])->firstOrFail();

        if ($user->hasVerifiedEmail()) {
            return redirect()
                ->route('login')
                ->with('status', 'Email sudah terverifikasi. Silakan masuk.');
        }

        $user->sendEmailVerificationNotification();

        return back()
            ->with('email', $user->email)
            ->with('verification_type', 'account')
            ->with('status', 'Link verifikasi baru sudah dikirim.');
    }

    public function forgotPassword()
    {
        return view('forgot_password');
    }

    public function sendResetLink(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $status = Password::sendResetLink(['email' => $data['email']]);

        if ($status !== Password::RESET_LINK_SENT) {
            return back()
                ->withErrors(['email' => __($status)])
                ->onlyInput('email');
        }

        return redirect()
            ->route('password.sent')
            ->with('email', $data['email'])
            ->with('verification_type', 'reset');
    }

    public function resetPasswordForm(Request $request, string $token)
    {
        return view('reset_password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset($data, function ($user, string $password) {
            $user->forceFill([
                'password' => $password,
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        });

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withErrors(['email' => __($status)])
                ->withInput($request->only('email'));
        }

        return redirect()
            ->route('login')
            ->with('status', 'Kata sandi berhasil diperbarui. Silakan masuk kembali.');
    }
}
