<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\WhatsappController;

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
    // Validasi input
    $validated = $request->validate([
        'reporter_name' => 'required|string|max:255',
        'contact'       => 'required|string|max:255',
        'division'      => 'required|string|max:255',
        'location_id'   => 'required|exists:locations,id',
        'description'   => 'required|string',
        'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // validasi gambar opsional
    ]);

    // Upload gambar jika ada
    $imageName = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('public/reports');
        $imageName = basename($imagePath); // simpan hanya nama file
    }

    // Simpan laporan ke database
    $report = Report::create([
        'reporter_name' => $validated['reporter_name'],
        'contact'       => $validated['contact'],
        'division'      => $validated['division'],
        'location_id'   => $validated['location_id'],
        'description'   => $validated['description'],
        'status'        => 'waiting',
        'image'         => $imageName,
    ]);

    $locationName = Location::find($validated['location_id'])->name ?? '-';

    // Kirim notifikasi ke semua IT Support (email + WA)
    $wa = app(WhatsappController::class);
    $itSupports = User::where('role', 'it_supp')->whereNotNull('contact')->get();

    foreach ($itSupports as $it) {
        // Kirim email
        Mail::raw(
            "Ada laporan baru dari {$report->reporter_name}.\nKlik untuk detail: " . url('/lapor/' . $report->id),
            function ($message) use ($it) {
                $message->to($it->email)
                        ->subject('Laporan Baru Masuk');
            }
        );

        // Kirim WA via Wablas
        $wa->sendToItSupport(
            $it->contact,
            $it->name ?? 'IT Support',
            $locationName,
            $report->reporter_name,
            $report->description,
            url('/report/' . $report->id)
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
