<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\WhatsappController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Signature;
use Illuminate\Support\Facades\Storage;



class ReportPublicController extends Controller
{
    public function landing()
    {
        return view('report.public-landing');
    }
    
    public function create()
    {
        return view('report.public-form', [
            'items' => Item::all(),
            'locations' => Location::all(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'reporter_name' => 'required|string|max:255',
                'contact'       => 'required|string|max:255',
                'division'      => 'required|string|max:255',
                'location_id'   => 'required|exists:locations,id',
                'item_id'       => 'required|exists:items,id',
                'description'   => 'required|string|max:1000',
                'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            // Normalisasi nomor telepon ke format +62
            $contact = $validated['contact'];
            
            // Hapus semua karakter non-digit
            $contact = preg_replace('/\D/', '', $contact);
            
            // Validasi panjang nomor
            if (strlen($contact) < 9 || strlen($contact) > 13) {
                throw new \Exception('Nomor WhatsApp harus antara 9-13 digit.');
            }
            
            // Jika belum dimulai dengan 62, tambahkan 62 di depan
            if (!str_starts_with($contact, '62')) {
                // Hapus 0 di depan jika ada (08xxx -> 8xxx)
                $contact = ltrim($contact, '0');
                // Tambahkan 62
                $contact = '62' . $contact;
            }
            
            $validated['contact'] = $contact;

            // Cek duplikasi laporan (mencegah spam)
            $recentReport = Report::where('contact', $contact)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->first();
                
            if ($recentReport) {
                throw new \Exception('Anda sudah mengirim laporan dalam 5 menit terakhir. Silakan tunggu sebentar.');
            }

            // Debug: Log file upload info
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                Log::info('File upload attempt', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'is_valid' => $file->isValid(),
                    'error' => $file->getError(),
                    'error_message' => $file->getErrorMessage()
                ]);
            }

            return DB::transaction(function () use ($request, $validated) {
                // Upload gambar jika ada dengan error handling lebih detail
                $imageName = null;
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    
                    if (!$file->isValid()) {
                        $errorCode = $file->getError();
                        $errorMessages = [
                            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize)',
                            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE)',
                            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
                            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload',
                            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan',
                            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
                            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP'
                        ];
                        
                        $errorMsg = $errorMessages[$errorCode] ?? 'Error upload tidak diketahui';
                        Log::error('File upload error', [
                            'error_code' => $errorCode,
                            'error_message' => $errorMsg,
                            'file_name' => $file->getClientOriginalName()
                        ]);
                        
                        throw new \Exception("Gagal upload gambar: {$errorMsg}");
                    }

                    // Cek ukuran file secara manual
                    if ($file->getSize() > 2048 * 1024) { // 2MB
                        throw new \Exception('Ukuran file terlalu besar. Maksimal 2MB.');
                    }

                    // Cek apakah direktori storage/app/public/reports ada
                    $reportsPath = storage_path('app/public/reports');
                    if (!file_exists($reportsPath)) {
                        mkdir($reportsPath, 0755, true);
                        Log::info('Created reports directory: ' . $reportsPath);
                    }

                    // Cek permission direktori
                    if (!is_writable($reportsPath)) {
                        Log::error('Reports directory is not writable: ' . $reportsPath);
                        throw new \Exception('Direktori upload tidak dapat ditulis.');
                    }

                    try {
                        // Generate unique filename
                        $extension = $file->getClientOriginalExtension();
                        $filename = time() . '_' . uniqid() . '.' . $extension;
                        
                        // Store file
                        $imagePath = $file->storeAs('reports', $filename, 'public');
                        
                        if (!$imagePath) {
                            throw new \Exception('Gagal menyimpan file ke storage.');
                        }
                        
                        $imageName = $filename;
                        
                        Log::info('File uploaded successfully', [
                            'filename' => $filename,
                            'path' => $imagePath,
                            'full_path' => storage_path('app/public/' . $imagePath)
                        ]);
                        
                        // Verifikasi file benar-benar tersimpan
                        if (!Storage::disk('public')->exists($imagePath)) {
                            throw new \Exception('File tidak ditemukan setelah upload.');
                        }
                        
                    } catch (\Exception $e) {
                        Log::error('File storage error: ' . $e->getMessage());
                        throw new \Exception('Gagal menyimpan gambar: ' . $e->getMessage());
                    }
                }

                // Simpan ke database
                $reportCode = 'RPT-' . date('Ymd') . '-' . strtoupper(Str::random(6));
                $reportPass = Str::random(8);

                // Hitung jumlah laporan sebelumnya dari nomor telepon yang sama
                $previousReportsCount = Report::where('contact', $validated['contact'])->count();

                $report = Report::create([
                    'reporter_name' => $validated['reporter_name'],
                    'contact'       => $validated['contact'],
                    'division'      => $validated['division'],
                    'location_id'   => $validated['location_id'],
                    'description'   => $validated['description'],
                    'status'        => 'waiting',
                    'image'         => $imageName,
                    'report_code'   => $reportCode,
                    'report_pass'   => $reportPass,
                ]);

                // Buat record di tabel report_details
                \App\Models\ReportDetail::create([
                    'report_id' => $report->id,
                    'item_id' => $validated['item_id'],
                    'tindakan' => 'Laporan masuk',
                    'uraian_masalah' => $validated['description'],
                ]);

                // Ambil nama lokasi
                $locationName = Location::find($validated['location_id'])->name ?? '-';

                // Kirim notifikasi (dalam try-catch terpisah agar tidak menggagalkan submit)
                try {
                    $this->sendNotifications($report);
                } catch (\Exception $e) {
                    Log::error('Notification sending failed: ' . $e->getMessage());
                    // Jangan throw error, biarkan laporan tersimpan
                }

                return redirect()->route('lapor.create')
                     ->with('success', $reportCode)
                     ->with('password', $reportPass)
                     ->with('report_count', $previousReportsCount + 1);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Report submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['image']) // Don't log file data
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ✅ PERBAIKAN: Method sendNotifications yang benar
    private function sendNotifications($report)
    {
        $wa = app(WhatsappController::class);
        
        // ✅ PANGGIL method sendReportNotifications yang sudah lengkap
        try {
            $wa->sendReportNotifications($report);
            Log::info('✅ WhatsApp notifications sent successfully for report: ' . $report->report_code);
        } catch (\Exception $e) {
            Log::error('❌ Failed to send WhatsApp notifications: ' . $e->getMessage());
        }
        
        // Email tetap dikirim terpisah untuk IT Support
        $itSupports = User::where('role', 'it_supp')->whereNotNull('contact')->get();
        
        foreach ($itSupports as $it) {
            try {
                Mail::raw(
                    "Ada laporan baru dari {$report->reporter_name}.\nKlik untuk detail: " . url('/lapor/' . $report->id),
                    function ($message) use ($it) {
                        $message->to($it->email)
                                ->subject('Laporan Baru Masuk');
                    }
                );
            } catch (\Exception $e) {
                Log::error("Failed to send email to {$it->email}: " . $e->getMessage());
            }
        }
    }
    public function show($id)
{
    $report = Report::with(['users', 'item', 'location', 'details'])
                ->where('report_code', $id)
                ->firstOrFail();

    return view('report.detail', compact('report'));
}



    public function trackForm()
{
    return view('report.track-form');
}

public function trackResult(Request $request)
{
    $report = Report::with(['itSupports', 'location']) // tambahkan relasi yang dibutuhkan
                    ->where('report_code', $request->report_code)
                    ->where('report_pass', $request->report_pass)
                    ->first();

    if (!$report) {
        return redirect()->back()->with('error', 'Kode atau Password salah.');
    }

    return view('report.track-result', ['report' => $report]);
}



    public function accept($id)
    {
        try {
            $report = Report::findOrFail($id);

            if ($report->status !== 'waiting') {
                return redirect()->route('lapor.show', $report->id)
                    ->with('warning', 'Laporan sudah diproses sebelumnya.');
            }

            $report->update(['status' => 'accepted']);

            return redirect()->route('lapor.show', $report->id)
                ->with('success', 'Laporan telah diterima.');
                
        } catch (\Exception $e) {
            Log::error('Failed to accept report: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses laporan.');
        }
    }

    public function showCheckForm()
{
    return view('report.report_check');
}

public function showByCode($report_code)
{
    $report = \App\Models\Report::where('report_code', $report_code)->firstOrFail();
    return view('public.report', compact('report'));
}



public function processCheck(Request $request)
{
    $request->validate([
        'report_code' => 'required',
        'report_pass' => 'required',
    ]);

    $report = Report::where('report_code', $request->report_code)
        ->where('report_pass', $request->report_pass)
        ->first();

    if (!$report) {
        return back()->withErrors(['Invalid' => 'Kode atau password tidak cocok.']);
    }

    return view('report.report_status', compact('report'));
}

public function uploadSignature(Request $request, $id)
{
    $report = Report::findOrFail($id);

    $request->validate([
        'signature_type' => 'required|in:digital,manual',
        'manual_signature' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        'digital_signature' => 'nullable|string',
    ]);

    if ($request->signature_type === 'manual' && $request->hasFile('manual_signature')) {
        $path = $request->file('manual_signature')->store('signatures', 'public');
    } elseif ($request->signature_type === 'digital' && $request->digital_signature) {
        $imageData = $request->digital_signature;
        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $imageName = 'signatures/' . uniqid() . '.png';
        Storage::disk('public')->put($imageName, base64_decode($image));
        $path = $imageName;
    } else {
        return back()->with('error', 'Tanda tangan tidak valid.');
    }

    // Simpan ke tabel `signatures`
    Signature::updateOrCreate([
    'report_id' => $report->id,
    'role' => 'user', // ✅ sesuai enum
], [
    'user_id' => null, // atau auth()->id() kalau login
    'signature_path' => $path,
    'is_checked' => true,
    'is_auto' => false,
    'signed_at' => now(),
]);


    return back()->with('success', 'Tanda tangan berhasil disimpan.');
}
}
