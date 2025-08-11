<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use App\Http\Controllers\WhatsappController;
use Illuminate\Support\Facades\DB;


class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['location'])->get();
        return view('report.index', compact('reports'));
    }

    public function create()
    {
        $items = Item::all();
        $locations = Location::all();
        return view('report.create', compact('items', 'locations'));
    }

    public function store(Request $request)
{
    // Validasi input dari form
    $validated = $request->validate([
        'reporter_name' => 'required|string|max:255',
        'contact'       => 'required|string|max:255',
        'item_id'       => 'required|exists:items,id',
        'location_id'   => 'required|exists:locations,id',
        'description'   => 'required|string',
    ]);

    // Tambahkan status default 'waiting'
    $validated['status'] = 'waiting';

    // Simpan laporan ke database
    $report = Report::create($validated);

    // Ambil nama lokasi dari ID
    $location = Location::find($validated['location_id'])->name ?? '-';

    // Kirim notifikasi WA ke semua IT Support
    $wa = new \App\Http\Controllers\WhatsappController(); // Pastikan import lengkap kalau beda namespace

    $itSupports = User::where('role', 'it_supp')->get();

    foreach ($itSupports as $it) {
        $wa->sendToItSupport(
            $it->contact,                                // Nomor WA penerima
            $it->name ?? 'IT Support',                   // Nama penerima
            $location,                                   // Lokasi laporan
            $validated['reporter_name'],                 // Nama pelapor
            $validated['description'],                   // Deskripsi masalah
            route('report.show', $report->id)            // Link ke detail laporan
        );
    }

    return redirect()->route('report.index')->with('success', 'Laporan berhasil dibuat dan dikirim ke IT Support.');
}


    public function show($id)
{
    $report = Report::with(['location', 'details.item', 'itSupports', 'signatures'])
                    ->findOrFail($id);

    // --- Hitung total laporan per item untuk pelapor & kontak yang sama
    $itemCounts = DB::table('report_details')
        ->join('reports', 'report_details.report_id', '=', 'reports.id')
        ->select('report_details.item_id', DB::raw('COUNT(*) AS total'))
        ->where('reports.reporter_name', $report->reporter_name)
        ->where('reports.contact',       $report->contact)
        ->groupBy('report_details.item_id')
        ->pluck('total', 'item_id');   // hasil: [item_id => total]

    $items          = Item::orderBy('name')->get();
    $allITSupports  = User::where('role', 'it_supp')->get();

    return view('it_support.show', compact(
        'report', 'items', 'allITSupports', 'itemCounts'
    ));
}


    public function accept($id)
    {
        $report = Report::findOrFail($id);

        if ($report->status === 'waiting') {
            $report->status = 'accepted';
            $report->save();
        }

        return back()->with('success', 'Laporan berhasil di-accept.');
    }

    public function updateSuratJalanDate(Request $request, $id)
{
    $request->validate([
        'surat_jalan_date' => 'required|date',
    ]);

    $report = Report::findOrFail($id);
    $report->surat_jalan_date = $request->surat_jalan_date;
    $report->save();

    return back()->with('success', 'Tanggal surat jalan berhasil disimpan.');
}

public function showDetailForm($id)
{
    $report = Report::with('location')->findOrFail($id);

    // Hitung jumlah laporan berdasarkan pelapor & item yang sama
    $reporterName = $report->reporter_name;
    $contact      = $report->contact;

    // Ambil semua item
    $items = Item::all();

    // Hitung laporan per item untuk pelapor ini
    $itemCounts = DB::table('report_details')
        ->join('reports', 'report_details.report_id', '=', 'reports.id')
        ->select('report_details.item_id', DB::raw('COUNT(*) as total'))
        ->where('reports.reporter_name', $reporterName)
        ->where('reports.contact', $contact)
        ->groupBy('report_details.item_id')
        ->pluck('total', 'item_id'); // hasil: [item_id => jumlah_laporan]

    return view('report.detail-form', compact('report', 'items', 'itemCounts'));
}
}
    