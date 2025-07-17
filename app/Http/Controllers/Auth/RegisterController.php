<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
    'name' => 'required|max:100',
    'email' => 'required|email|unique:users',
    'password' => 'required|confirmed|min:6',
    'role' => 'required|in:superadmin,it_supp',
    'jabatan' => 'required|string|max:100',
    'departemen' => 'required|string|max:100',
]);

$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    'role' => $request->role,
    'jabatan' => $request->jabatan,
    'departemen' => $request->departemen,
]);


        // Auth::login($user);

        return redirect()->route('login')->with('success', 'Registrasi berhasil. Silakan login.');
    }
}
