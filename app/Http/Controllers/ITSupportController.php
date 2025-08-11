<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use App\Models\Item;
use App\Models\ReportDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Signature;
use App\Http\Controllers\WhatsappController;
use Illuminate\Support\Facades\Log;


class ITSupportController extends Controller
{
    /**
     * Menampilkan semua laporan waiting/accepted
     */
    public function showReports()
    {
        $reports = Report::with(['item', 'location', 'itSupports', 'details'])
            ->whereIn('status', ['waiting', 'accepted'])
            ->get();

        $allITSupports = User::where('role', 'it_supp')->get();
        $items = Item::all();

        return view('it_support.reports', compact('reports', 'allITSupports', 'items'));
    }

    /**
     * IT Support menerima laporan
     */
    public function accept(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        if ($report->status !== 'waiting') {
            return back()->with('error', 'Laporan sudah diambil.');
        }

        $report->status = 'accepted';
        $report->save();

        // Tambah user yang login
        $report->itSupports()->attach(Auth::id());

        // Tambah tim (jika ada)
        if ($request->has('team')) {
            foreach ($request->team as $uid) {
                if ($uid != Auth::id()) {
                $report->itSupports()->attach($uid);
                }
            }
        }

        return back()->with('success', 'Laporan berhasil diterima.');
    }

    /**
     * Menambahkan detail tindakan dan uraian masalah
     */
    public function addDetail(Request $request, $reportId)
{
    $report = Report::findOrFail($reportId);

    $validated = $request->validate([
    'item_id' => 'required|exists:items,id',
    'tindakan' => 'required|string',
    'uraian_masalah' => 'required|string',
    ]);

    // Simpan detail tindakan
    ReportDetail::create([
    'report_id' => $report->id,
    'item_id' => $validated['item_id'],
    'tindakan' => $validated['tindakan'],
    'uraian_masalah' => $validated['uraian_masalah'],
    ]);

    if ($request->filled('surat_jalan_date')) {
        $report->surat_jalan_date = $request->surat_jalan_date;
        $report->save();
}


    return back()->with('success', 'Detail laporan & tanggal surat jalan berhasil disimpan.');
}


    /**
     * Generate PDF surat tugas
     */
    public function generateSuratTugas($id)
{
    $report = Report::with(['item', 'location', 'itSupports', 'signatures'])->findOrFail($id);

    if ($report->status !== 'accepted') {
        return back()->with('error', 'Surat tugas hanya bisa dicetak setelah laporan di-accept.');
    }

    if (!$report->surat_jalan_date) {
        return back()->with('error', 'Tanggal surat jalan wajib diisi sebelum mencetak surat tugas.');
    }

    $tanggalSurat = \Carbon\Carbon::parse($report->surat_jalan_date)->translatedFormat('d F Y');
    $signatures = $report->signatures->keyBy('role');

    $pdf = Pdf::loadView('it_support.surat', [
        'report' => $report,
        'tanggalSurat' => $tanggalSurat,
        'signatures' => $signatures
    ]);

    return $pdf->stream('surat_tugas.pdf');
}



public function pindahDivisi(Request $request, $id)
{
    $report = Report::findOrFail($id);

    $request->validate([
        'catatan' => 'required|string|max:1000',
    ]);

    // Tandai sebagai "completed" karena bukan tugas IT Support (opsional, bisa kamu ganti logika sesuai kebutuhan)
    $report->status = 'completed';
    $report->save();

    // Simpan ke log / email atau notifikasi ke divisi lain jika perlu
    // Untuk sekarang hanya menampilkan sukses
    return back()->with('success', 'Laporan telah dipindahkan ke divisi lain.');
}

public function editDetail($detail_id)
{
    $detail = ReportDetail::with('item')->findOrFail($detail_id);
    $items = Item::all();
    return view('report.edit-detail', compact('detail', 'items'));
}

public function updateDetail(Request $request, $detail_id)
{
    $request->validate([
        'item_id' => 'required|exists:items,id',
        'uraian_masalah' => 'required|string',
        'tindakan' => 'required|string',
    ]);

    $detail = ReportDetail::findOrFail($detail_id);
    $detail->update([
        'item_id' => $request->item_id,
        'uraian_masalah' => $request->uraian_masalah,
        'tindakan' => $request->tindakan,
    ]);

    return redirect()->route('report.show', $detail->report_id)->with('success', 'Detail tindakan berhasil diperbarui.');
}

public function simpanTanggalSurat(Request $request, $id)
{
    $request->validate([
        'surat_jalan_date' => 'required|date',
    ]);

    $report = Report::findOrFail($id);
    $report->surat_jalan_date = $request->surat_jalan_date;
    $report->save();

    return back()->with('success', 'Tanggal surat jalan berhasil disimpan.');
}

public function nextDay(Request $request, $id)
{
    $report = Report::with(['item', 'location', 'user'])->findOrFail($id);

    // Pastikan status 'accepted'
    if ($report->status !== 'accepted') {
        return back()->with('error', 'Laporan belum berstatus accepted.');
    }

    // Generate ulang report_code dan report_pass
    $report->report_code = 'RPT-' . date('Ymd') . '-' . strtoupper(Str::random(6));
    $report->report_pass = Str::random(8);

    // Hapus tanda tangan dari tabel signatures
    Signature::where('report_id', $report->id)->delete();

    $report->save();

    // Kirim ulang pesan WhatsApp ke semua IT Support yang terkait dengan laporan ini
    $itSupports = $report->users; // Pastikan relasi 'users' tersedia dan sesuai
    $link = route('report.show', $report->id); // Ganti dengan route detail laporan kamu

    foreach ($itSupports as $user) {
        app(WhatsappController::class)->sendToItSupport(
            $user->contact,
            $user->division ?? $user->position ?? '-', // fallback
            $report->location->name ?? '-',
            $report->reporter_name,
            $report->description,
            $link
        );
    }

    return back()->with('success', 'Next Day berhasil: kode/password di-reset, tanda tangan dihapus, dan notifikasi dikirim ulang.');
}

    /**
     * Tambah item baru ke laporan yang sudah ada
     */
    public function addItemToReport(Request $request, $report_id)
    {
        try {
            $request->validate([
                'item_id' => 'required|exists:items,id',
                'tindakan' => 'required|string|max:255',
                'uraian_masalah' => 'required|string',
            ]);

            $report = Report::findOrFail($report_id);
            
            // Cek apakah item sudah ada di laporan ini
            $existingItem = ReportDetail::where('report_id', $report_id)
                ->where('item_id', $request->item_id)
                ->first();
                
            if ($existingItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item ini sudah ada dalam laporan ini.'
                ], 400);
            }

            // Buat record baru di report_details
            ReportDetail::create([
                'report_id' => $report_id,
                'item_id' => $request->item_id,
                'tindakan' => $request->tindakan,
                'uraian_masalah' => $request->uraian_masalah,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil ditambahkan ke laporan.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to add item to report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan item.'
            ], 500);
        }
    }

    /**
     * Update item dalam laporan
     */
    public function updateReportItem(Request $request, $report_id, $detail_id)
    {
        try {
            $request->validate([
                'item_id' => 'required|exists:items,id',
                'tindakan' => 'required|string|max:255',
                'uraian_masalah' => 'required|string',
            ]);

            $detail = ReportDetail::where('report_id', $report_id)
                ->where('id', $detail_id)
                ->firstOrFail();

            // Cek apakah item sudah ada di laporan ini (kecuali yang sedang diupdate)
            $existingItem = ReportDetail::where('report_id', $report_id)
                ->where('item_id', $request->item_id)
                ->where('id', '!=', $detail_id)
                ->first();
                
            if ($existingItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item ini sudah ada dalam laporan ini.'
                ], 400);
            }

            $detail->update([
                'item_id' => $request->item_id,
                'tindakan' => $request->tindakan,
                'uraian_masalah' => $request->uraian_masalah,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil diupdate.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update report item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate item.'
            ], 500);
        }
    }

    /**
     * Hapus item dari laporan
     */
    public function removeItemFromReport($report_id, $detail_id)
    {
        try {
            $detail = ReportDetail::where('report_id', $report_id)
                ->where('id', $detail_id)
                ->firstOrFail();

            // Pastikan tidak menghapus semua item dari laporan
            $totalItems = ReportDetail::where('report_id', $report_id)->count();
            if ($totalItems <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus item terakhir dari laporan.'
                ], 400);
            }

            $detail->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari laporan.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to remove item from report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus item.'
            ], 500);
        }
    }
}
