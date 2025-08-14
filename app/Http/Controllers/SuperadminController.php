<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use App\Models\Item;
use App\Models\ReportDetail;
use App\Models\Signature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Location;

class SuperadminController extends Controller
{
    /**
     * Menampilkan semua laporan untuk superadmin
     */
    public function showReports()
    {
        $reports = Report::with(['item', 'location', 'itSupports', 'details'])
            ->get();

        return view('superadmin.reports', compact('reports'));
    }

    /**
     * Menampilkan detail laporan untuk superadmin
     */
    public function showReport($id)
    {
        $report = Report::with(['location', 'details.item', 'itSupports', 'signatures'])
                    ->findOrFail($id);

        $items = Item::orderBy('name')->get();
        $allITSupports = User::where('role', 'it_supp')->get();

        return view('superadmin.show', compact('report', 'items', 'allITSupports'));
    }
    
    /**
     * Menyimpan tanda tangan superadmin untuk laporan
     */
    public function storeSignature(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        // Validasi request
        if ($request->has('use_saved_signature') && $request->use_saved_signature == '1') {
            // Menggunakan tanda tangan yang tersimpan
            $request->validate([
                'use_saved_signature' => 'required|in:1',
            ]);
        } else {
            // Menggunakan tanda tangan yang baru digambar
            $request->validate([
                'signature' => 'required|string', // base64 image
            ]);
        }

        $userId = Auth::id();
        $user = Auth::user();
        
        // Verifikasi bahwa user adalah superadmin
        if ($user->role !== 'superadmin') {
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
            // Decode base64 image dari tanda tangan yang baru digambar
            $base64Image = $request->signature;
            $image = str_replace('data:image/png;base64,', '', $base64Image);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            // Simpan file ke storage
            $filename = 'signatures/ttd_superadmin_' . $userId . '_report_' . $report->id . '.png';
            Storage::disk('public')->put($filename, $imageData);
            
            // Jika user belum memiliki tanda tangan tersimpan, tanyakan apakah ingin menyimpannya
            if (!$user->signature_path && $request->has('save_for_later') && $request->save_for_later == '1') {
                $user->signature_path = $filename;
                $user->save();
            }
        }

        // Simpan atau update di tabel signatures
        $signature = Signature::updateOrCreate(
            [
                'report_id' => $report->id,
                'user_id' => $userId,
                'role' => 'superadmin',
            ],
            [
                'signature_path' => $filename,
                'signed_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Tanda tangan superadmin berhasil disimpan.');
    }
    
    /**
     * Generate PDF surat tugas untuk superadmin
     * Tanpa validasi tanggal surat jalan
     */
    public function generateSuratTugas($id)
    {
        $report = Report::with(['item', 'location', 'itSupports', 'signatures'])->findOrFail($id);
        
        // Jika tanggal surat jalan belum diisi, gunakan tanggal hari ini
        if (!$report->surat_jalan_date) {
            $report->surat_jalan_date = Carbon::now()->format('Y-m-d');
        }
        
        // Format tanggal untuk ditampilkan di surat
        $tanggalSurat = Carbon::parse($report->surat_jalan_date)->translatedFormat('d F Y');
            
        $signatures = $report->signatures->keyBy('role');

        $pdf = Pdf::loadView('it_support.surat', [
            'report' => $report,
            'signatures' => $signatures,
            'tanggalSurat' => $tanggalSurat
        ]);

        return $pdf->stream('surat_tugas.pdf');
    }
    
    /**
     * Menampilkan halaman manajemen kategori dan cabang
     */
    public function manageData()
    {
        $items = Item::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        
        return view('superadmin.manage-data', compact('items', 'locations'));
    }
    
    /**
     * Menampilkan halaman manajemen kategori
     */
    public function manageCategories()
    {
        $items = Item::orderBy('name')->get();
        
        return view('superadmin.manage-categories', compact('items'));
    }
    
    /**
     * Menampilkan halaman manajemen cabang
     */
    public function manageBranches()
    {
        $locations = Location::orderBy('name')->get();
        
        return view('superadmin.manage-branches', compact('locations'));
    }
    
    /**
     * Menyimpan cabang baru
     */
    public function storeBranch(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
            'category' => 'required|in:SJM,Non SJM'
        ]);
        
        Location::create([
            'name' => $request->name,
            'category' => $request->category
        ]);
        
        return redirect()->back()->with('success', 'Cabang berhasil ditambahkan!');
    }
    
    /**
     * Menyimpan kategori baru
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:items,name'
        ]);
        
        Item::create([
            'name' => $request->name
        ]);
        
        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }
    
    /**
     * Menyimpan cabang baru
     */
    
    
    /**
     * Menghapus kategori
     */
    public function deleteCategory($id)
    {
        $item = Item::findOrFail($id);
        
        // Cek apakah kategori masih digunakan dalam laporan
        if ($item->reports()->count() > 0) {
            return redirect()->back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan dalam laporan!');
        }
        
        $item->delete();
        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
    
    /**
     * Mengupdate kategori
     */
    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:items,name,' . $id
        ]);
        
        $item = Item::findOrFail($id);
        $item->update([
            'name' => $request->name
        ]);
        
        return redirect()->back()->with('success', 'Kategori berhasil diperbarui!');
    }
    
    /**
     * Mengupdate cabang
     */
    public function updateBranch(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $id,
            'category' => 'required|in:SJM,Non SJM'
        ]);
        
        $location = Location::findOrFail($id);
        $location->update([
            'name' => $request->name,
            'category' => $request->category
        ]);
        
        return redirect()->back()->with('success', 'Cabang berhasil diperbarui!');
    }
    
    /**
     * Menghapus cabang
     */
    public function deleteBranch($id)
    {
        $location = Location::findOrFail($id);
        
        // Cek apakah cabang masih digunakan dalam laporan
        if ($location->reports()->count() > 0) {
            return redirect()->back()->with('error', 'Cabang tidak dapat dihapus karena masih digunakan dalam laporan!');
        }
        
        $location->delete();
        return redirect()->back()->with('success', 'Cabang berhasil dihapus!');
    }
}