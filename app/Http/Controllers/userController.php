<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class userController extends Controller
{
    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'shoe_size' => ['nullable', 'integer', 'min:20', 'max:60'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->user()->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function redirectDashboard()
    {
        $user = Auth::user();

        return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'customer.dashboard');
    }
}
