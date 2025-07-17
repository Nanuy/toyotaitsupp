<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class ReportPublicController extends Controller
{
    // Tampilkan form laporan publik
    public function create()
    {
        return view('report.public-form', [
            'items' => Item::all(),
            'locations' => Location::all(),
        ]);
    }

    // Simpan laporan dari form publik
    public function store(Request $request)
    {
        $validated = $request->validate([
    'reporter_name' => 'required|string|max:255',
    'contact' => 'required|string|max:255',
    'division' => 'required|string|max:255',
    'location_id' => 'required|exists:locations,id',
    'description' => 'required|string',
]);


$report = Report::create([
    'reporter_name' => $validated['reporter_name'],
    'contact'       => $validated['contact'],
    'division'      => $validated['division'],
    'location_id'   => $validated['location_id'],
    'description'   => $validated['description'],
    'status'        => 'waiting',
]);


        // Notifikasi ke semua IT Support
        $itSupports = User::where('role', 'it_supp')->get();

        foreach ($itSupports as $it) {
            Mail::raw(
                "Ada laporan baru dari {$report->reporter_name}.\nKlik untuk detail: " . url('/lapor/' . $report->id),
                function ($message) use ($it) {
                    $message->to($it->email)
                            ->subject('Laporan Baru Masuk');
                }
            );
        }

        return redirect()->route('lapor.create')->with('success', 'Laporan berhasil dikirim!');
    }

    // Tampilkan detail laporan publik
    public function show($id)
    {
        $report = Report::with(['item', 'location'])->findOrFail($id);
        return view('report.detail', compact('report'));
    }

    // Tombol accept oleh IT Support
    public function accept($id)
    {
        $report = Report::findOrFail($id);

        if ($report->status === 'waiting') {
            $report->status = 'accepted';
            $report->save();
        }

        return redirect()->route('lapor.show', $report->id)->with('success', 'Laporan telah diterima.');
    }
}
