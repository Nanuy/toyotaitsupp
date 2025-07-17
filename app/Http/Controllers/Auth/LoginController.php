<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        if ($user->role === 'superadmin') {
            return redirect()->route('dashboard.superadmin');
        } elseif ($user->role === 'it_supp') {
            return redirect()->route('it_support.reports');
        }

        return redirect('/')->with('error', 'Role tidak dikenali');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah',
    ]);
}



    public function logout(Request $request)
    {
        Auth::logout(); // Hapus session Laravel
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Logout berhasil');
    }
}
