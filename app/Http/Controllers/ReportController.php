<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;

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
        $validated = $request->validate([
            'reporter_name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'item_id' => 'required|exists:items,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string',
        ]);

        $validated['status'] = 'waiting';

        Report::create($validated);

        return redirect()->route('report.index')->with('success', 'Laporan berhasil dibuat.');
    }

    public function show($id)
    {
        $report = Report::with(['location', 'details.item', 'itSupports'])->findOrFail($id);
        $items = Item::all();
        $allITSupports = User::where('role', 'it_supp')->get();

        return view('it_support.show', compact('report', 'items', 'allITSupports'));
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

}
