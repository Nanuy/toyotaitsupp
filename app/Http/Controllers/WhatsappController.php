<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappController extends Controller
{
    public function sendReportNotifications($report)
    {
        // Kirim ke semua IT Support
        $itSupports = \App\Models\User::where('role', 'it_supp')->get();

        foreach ($itSupports as $it) {
            $this->sendToItSupport(
                $it->contact,
                $it->name,
                $report->location->name ?? 'Lokasi tidak diketahui',
                $report->reporter_name,
                $report->description,
                url('/report-detail/' . $report->id)
            );
        }

        // ✅ PERBAIKAN: Kirim ke Reporter (ini yang hilang!)
        $this->sendToReporter(
            $report->contact,
            $report->reporter_name,
            $report->report_code,
            $report->report_pass,
            url('/cek-laporan')
        );
    }

    public function sendToItSupport($targetPhone, $receiverName, $location, $reporterName, $description, $link)
    {
        $message = "📢 *Toyota IT Support*\n\n"
                 . "👤 Nama: {$reporterName}\n"
                 . "📞 Kontak: {$targetPhone}\n"
                 . "👩‍💼 Division: {$receiverName}\n"
                 . "🏢 Cabang: {$location}\n"
                 . "📝 Masalah: {$description}\n\n"
                 . "🔗 Lihat Laporan: \n{$link}";

        // ✅ FONNTE: Endpoint baru
        $url = 'https://api.fonnte.com/send';

        $headers = [
            'Authorization' => env('FONNTE_API_KEY'),
            'Content-Type'  => 'application/json',
        ];

        $payload = [
            'target' => $this->formatPhone($targetPhone),
            'message' => $message,
            'countryCode' => '62', // Indonesia
        ];

        try {
            $response = Http::withHeaders($headers)
                ->withOptions([
                    'verify' => env('APP_ENV') === 'production' ? true : false,
                    'timeout' => 30,
                    'connect_timeout' => 10,
                ])
                ->post($url, $payload);

            Log::info('📨 WA IT Support Debug (Fonnte)', [
                'target' => $targetPhone,
                'url' => $url,
                'headers' => $headers,
                'payload' => $payload,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return $response->json();

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('❌ Fonnte Connection Error: ' . $e->getMessage(), [
                'target' => $targetPhone,
                'url' => $url,
                'error_code' => $e->getCode(),
            ]);

            return ['status' => false, 'message' => 'WhatsApp notification failed'];

        } catch (\Exception $e) {
            Log::error('❌ Fonnte General Error: ' . $e->getMessage(), [
                'target' => $targetPhone,
                'error' => $e->getTrace(),
            ]);

            return ['status' => false, 'message' => 'WhatsApp notification failed'];
        }
    }

    public function sendToReporter($targetPhone, $reporterName, $reportCode, $reportPass, $reportLink)
    {
        $message = "📢 *Toyota IT Support*\n\n"
                 . "Terima kasih telah membuat laporan, {$reporterName}.\n\n"
                 . "📄 *Kode:* {$reportCode}\n"
                 . "🔑 *Password:* {$reportPass}\n"
                 . "📍 *Link:* \n{$reportLink}\n\n"
                 . "Gunakan kode dan password untuk memantau status laporan Anda.";

        // ✅ FONNTE: Endpoint yang sama
        $url = 'https://api.fonnte.com/send';

        $headers = [
            'Authorization' => env('FONNTE_API_KEY'),
            'Content-Type'  => 'application/json',
        ];

        $payload = [
            'target' => $this->formatPhone($targetPhone),
            'message' => $message,
            'countryCode' => '62', // Indonesia
        ];

        try {
            $response = Http::withHeaders($headers)
                ->withOptions([
                    'verify' => env('APP_ENV') === 'production' ? true : false,
                    'timeout' => 30,
                    'connect_timeout' => 10,
                ])
                ->post($url, $payload);

            Log::info('📨 WA Reporter Debug (Fonnte)', [
                'phone' => $targetPhone,
                'url' => $url,
                'headers' => $headers,
                'payload' => $payload,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('❌ Gagal kirim WA ke reporter (Fonnte): ' . $e->getMessage(), [
                'target' => $targetPhone,
                'url' => $url,
                'error' => $e->getTrace(),
            ]);
            
            return ['status' => false, 'message' => 'WhatsApp notification failed'];
        }
    }

    private function formatPhone($phone)
    {
        // Format nomor untuk Fonnte
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Jika dimulai dengan 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // Jika belum ada country code, tambahkan 62
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }
}