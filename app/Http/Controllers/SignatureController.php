<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Signature;
// use App\Models\Report;
use App\Models\ReportUser;


class SignatureController extends Controller
{
    public function store(Request $request)
    {
        // Validasi request berdasarkan opsi yang dipilih
        if ($request->has('use_saved_signature') && $request->use_saved_signature == '1') {
            // Menggunakan tanda tangan yang tersimpan
            $request->validate([
                'report_id' => 'required|exists:reports,id',
                'use_saved_signature' => 'required|in:1',
            ]);
        } else {
            // Menggunakan tanda tangan yang baru digambar
            $request->validate([
                'report_id' => 'required|exists:reports,id',
                'signature' => 'required|string', // base64 image
            ]);
        }

        $reportId = $request->report_id;
        $userId   = Auth::id();
        $user = Auth::user();

        // ✅ Cek apakah user IT Support yang ditugaskan untuk report ini
        $isAssigned = ReportUser::where('report_id', $reportId)
            ->where('user_id', $userId)
            ->exists();

        if (! $isAssigned) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak memiliki izin untuk menandatangani laporan ini.'
            ], 403);
        }

        // Tentukan path tanda tangan
        $filename = '';
        
        if ($request->has('use_saved_signature') && $request->use_saved_signature == '1') {
            // Gunakan tanda tangan yang tersimpan di profil user
            if (!$user->signature_path) {
                return redirect()->back()->with('error', 'Anda belum memiliki tanda tangan tersimpan.');
            }
            $filename = $user->signature_path;
        } else {
            // ✅ Decode base64 image
            $base64Image = $request->signature;
            $image = str_replace('data:image/png;base64,', '', $base64Image);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            // ✅ Simpan file ke storage
            $filename = 'signatures/ttd_user_' . $userId . '_report_' . $reportId . '.png';
            Storage::disk('public')->put($filename, $imageData);

            // Jika user belum memiliki tanda tangan tersimpan, simpan juga di profil
            if (!$user->signature_path) {
                $user->signature_path = $filename;
                $user->save();
            }
        }

        // ✅ Simpan atau update di tabel signatures
        $signature = Signature::updateOrCreate(
            [
                'report_id' => $reportId,
                'user_id' => $userId,
            ],
            [
                'role' => 'it_supp',
                'signature_path' => $filename,
                'signed_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Tanda tangan berhasil disimpan.');

    }

    public function getUserSignature(Request $request, $reportId)
    {
        $userId = Auth::id();

        // Cek apakah user assigned di report ini
        $isAssigned = \App\Models\ReportUser::where('report_id', $reportId)
            ->where('user_id', $userId)
            ->exists();

        if (! $isAssigned) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak memiliki izin untuk melihat tanda tangan.'
            ], 403);
        }

        // ✅ Ambil dari tabel signatures berdasarkan report dan user
        $signature = Signature::where('report_id', $reportId)
            ->where('user_id', $userId)
            ->first();

        if (! $signature || ! $signature->signature_path) {
            return response()->json([
                'status' => false,
                'message' => 'Tanda tangan belum tersedia.'
            ]);
        }

        $url = asset('storage/' . $signature->signature_path);

        return response()->json([
            'status' => true,
            'signature_url' => $url,
            'signed_at' => $signature->signed_at
        ]);
    }
}