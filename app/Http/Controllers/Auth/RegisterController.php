<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Exception;

class RegisterController extends Controller
{
    /**
     * Tampilkan form registrasi
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi user baru
     */
    public function customRegister(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|confirmed|min:6',
            'jabatan' => 'required|string|max:255',
            'departemen' => 'required|string|max:255', 
            'contact' => 'required|string|max:30',
            'role' => 'required|in:it_supp,superadmin',
            'signature' => 'required|image|mimes:png|max:512',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
            'contact.required' => 'Kontak wajib diisi.',
            'contact.max' => 'Kontak maksimal 30 karakter.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'departemen.required' => 'Departemen wajib diisi.',
            'role.required' => 'Role wajib dipilih.',
            'signature.required' => 'File tanda tangan wajib diupload.',
            'signature.mimes' => 'File harus berformat PNG.',
            'signature.max' => 'File maksimal 500KB.',
        ]);

        try {
            // Upload file signature
            $signaturePath = null;
            if ($request->hasFile('signature') && $request->file('signature')->isValid()) {
                // Pastikan direktori ada
                if (!Storage::disk('public')->exists('signatures')) {
                    Storage::disk('public')->makeDirectory('signatures');
                }

                $file = $request->file('signature');
                $fileName = 'signature_' . time() . '_' . uniqid() . '.png';
                $signaturePath = $file->storeAs('signatures', $fileName, 'public');
            }

            // Buat user baru
            User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'jabatan' => $validatedData['jabatan'],
                'departemen' => $validatedData['departemen'],
                'contact' => $validatedData['contact'],
                'role' => $validatedData['role'],
                'signature_path' => $signaturePath,
            ]);

            return redirect()->route('register')->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');

        } catch (Exception $e) {
            // Hapus file jika ada error
            if (isset($signaturePath) && $signaturePath && Storage::disk('public')->exists($signaturePath)) {
                Storage::disk('public')->delete($signaturePath);
            }

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }
}