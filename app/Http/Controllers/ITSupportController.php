<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\User;
use App\Models\Item;
use App\Models\ReportDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;


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
        'surat_jalan_date' => 'required|date',
    ]);

    // Simpan detail tindakan
    ReportDetail::create([
        'report_id' => $report->id,
        'item_id' => $validated['item_id'],
        'tindakan' => $validated['tindakan'],
        'uraian_masalah' => $validated['uraian_masalah'],
    ]);

    // Simpan tanggal surat jalan ke tabel reports
    $report->surat_jalan_date = $validated['surat_jalan_date'];
    $report->save();

    return back()->with('success', 'Detail laporan & tanggal surat jalan berhasil disimpan.');
}


    /**
     * Generate PDF surat tugas
     */
    public function generateSuratTugas($id)
{
    $report = Report::with(['item', 'location', 'itSupports'])->findOrFail($id);

    if ($report->status !== 'accepted') {
        return back()->with('error', 'Surat tugas hanya bisa dicetak setelah laporan di-accept.');
    }

    // Ambil tanggal surat jalan jika ada
    $tanggalSurat = $report->surat_jalan_date
        ? \Carbon\Carbon::parse($report->surat_jalan_date)->translatedFormat('d F Y')
        : '-';

    $pdf = Pdf::loadView('it_support.surat', [
        'report' => $report,
        'tanggalSurat' => $tanggalSurat
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


}
