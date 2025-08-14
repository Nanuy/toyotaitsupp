<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Exception;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user
     */
    public function index()
    {
        $users = User::all();
        return view('user.index', compact('users'));
    }

    /**
     * Menampilkan form untuk membuat user baru
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Menyimpan user baru ke database
     */
    public function store(Request $request)
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
            'signature' => 'nullable|image|mimes:png|max:512',
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

            return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan!');

        } catch (Exception $e) {
            // Hapus file jika ada error
            if (isset($signaturePath) && $signaturePath && Storage::disk('public')->exists($signaturePath)) {
                Storage::disk('public')->delete($signaturePath);
            }

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menambahkan user. Silakan coba lagi.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    /**
     * Menampilkan detail user
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('user.show', compact('user'));
    }

    /**
     * Menampilkan form untuk mengedit user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('user.edit', compact('user'));
    }

    /**
     * Mengupdate data user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validasi input
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'jabatan' => 'required|string|max:255',
            'departemen' => 'required|string|max:255', 
            'contact' => 'required|string|max:30',
            'role' => 'required|in:it_supp,superadmin',
            'signature' => 'nullable|image|mimes:png|max:512',
        ];

        // Jika password diisi, tambahkan validasi password
        if ($request->filled('password')) {
            $rules['password'] = 'required|confirmed|min:6';
        }

        $validatedData = $request->validate($rules, [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
            'contact.required' => 'Kontak wajib diisi.',
            'contact.max' => 'Kontak maksimal 30 karakter.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'departemen.required' => 'Departemen wajib diisi.',
            'role.required' => 'Role wajib dipilih.',
            'signature.mimes' => 'File harus berformat PNG.',
            'signature.max' => 'File maksimal 500KB.',
        ]);

        try {
            // Upload file signature jika ada
            if ($request->hasFile('signature') && $request->file('signature')->isValid()) {
                // Hapus file lama jika ada
                if ($user->signature_path && Storage::disk('public')->exists($user->signature_path)) {
                    Storage::disk('public')->delete($user->signature_path);
                }

                // Pastikan direktori ada
                if (!Storage::disk('public')->exists('signatures')) {
                    Storage::disk('public')->makeDirectory('signatures');
                }

                $file = $request->file('signature');
                $fileName = 'signature_' . time() . '_' . uniqid() . '.png';
                $signaturePath = $file->storeAs('signatures', $fileName, 'public');
                
                $user->signature_path = $signaturePath;
            }

            // Update data user
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->jabatan = $validatedData['jabatan'];
            $user->departemen = $validatedData['departemen'];
            $user->contact = $validatedData['contact'];
            $user->role = $validatedData['role'];
            
            // Update password jika diisi
            if ($request->filled('password')) {
                $user->password = Hash::make($validatedData['password']);
            }

            $user->save();

            return redirect()->route('user.index')->with('success', 'User berhasil diperbarui!');

        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui user. Silakan coba lagi.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    /**
     * Menghapus user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        try {
            // Hapus file signature jika ada
            if ($user->signature_path && Storage::disk('public')->exists($user->signature_path)) {
                Storage::disk('public')->delete($user->signature_path);
            }

            $user->delete();
            return redirect()->route('user.index')->with('success', 'User berhasil dihapus!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus user. Silakan coba lagi.']);
        }
    }

    /**
     * Menampilkan form untuk mengedit profil sendiri
     */
    public function editProfile()
    {
        $user = auth()?->user();
        return view('user.profile', compact('user'));
    }

    /**
     * Mengupdate profil sendiri
     */
    public function updateProfile(Request $request)
    {
$user = auth()?->user();

        // Validasi input
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'contact' => 'required|string|max:30',
            'signature' => 'nullable|image|mimes:png|max:512',
        ];

        // Jika password diisi, tambahkan validasi password
        if ($request->filled('password')) {
            $rules['password'] = 'required|confirmed|min:6';
        }

        $validatedData = $request->validate($rules);

        try {
            // Upload file signature jika ada
            if ($request->hasFile('signature') && $request->file('signature')->isValid()) {
                // Hapus file lama jika ada
                if ($user->signature_path && Storage::disk('public')->exists($user->signature_path)) {
                    Storage::disk('public')->delete($user->signature_path);
                }

                // Pastikan direktori ada
                if (!Storage::disk('public')->exists('signatures')) {
                    Storage::disk('public')->makeDirectory('signatures');
                }

                $file = $request->file('signature');
                $fileName = 'signature_' . time() . '_' . uniqid() . '.png';
                $signaturePath = $file->storeAs('signatures', $fileName, 'public');
                
                $user->signature_path = $signaturePath;
            }

            // Update data user
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->contact = $validatedData['contact'];
            
            // Update password jika diisi
            if ($request->filled('password')) {
                $user->password = Hash::make($validatedData['password']);
            }

            $user->save();

            return redirect()->route('user.profile')->with('success', 'Profil berhasil diperbarui!');

        } catch (Exception $e) {
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui profil. Silakan coba lagi.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }
}