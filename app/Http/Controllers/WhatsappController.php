<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappController extends Controller
{
    public function sendToItSupport($targetPhone, $receiverName, $location, $reporterName, $description, $link)
    {
        $message = "📢 *Toyota IT Support*\n\n"
                 . "👤 Nama: {$reporterName}\n"
                 . "📞 Kontak: {$targetPhone}\n"
                 . "👩‍💼 division: {$receiverName}\n"
                 . "🏢 Cabang: {$location}\n"
                 . "📝 Masalah: {$description}\n\n"
                 . "🔗 Lihat Laporan: \n{$link}";

        $url = env('WABLAS_API_BASE', 'https://bdg.wablas.com') . '/api/v2/send-message';

        $headers = [
            'Authorization' => env('WABLAS_API_KEY'),
            'Content-Type'  => 'application/json',
        ];

        // Tambahkan secret key jika ada
        if (env('WABLAS_SECRET_KEY')) {
            $headers['X-Secret-Key'] = env('WABLAS_SECRET_KEY');
        }

        $payload = [
            'device' => env('WABLAS_DEVICE'),
            'data' => [[
                'phone'   => $this->formatPhone($targetPhone),
                'message' => $message,
            ]]
        ];

        try {
            // Kirim request dengan SSL options
            $response = Http::withHeaders($headers)
                ->withOptions([
                    'verify' => env('APP_ENV') === 'production' ? true : false, // Disable SSL untuk development
                    'timeout' => 30, // Timeout 30 detik
                    'connect_timeout' => 10, // Connection timeout 10 detik
                ])
                ->post($url, $payload);

            // Debug: tampilkan semua respon dan log
            Log::info('📨 WA Wablas Debug', [
                'target' => $targetPhone,
                'url' => $url,
                'headers' => $headers,
                'payload' => $payload,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return $response->json();

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Log error tanpa menghentikan proses
            Log::error('❌ Wablas Connection Error: ' . $e->getMessage(), [
                'target' => $targetPhone,
                'url' => $url,
                'error_code' => $e->getCode(),
            ]);

            // Return response kosong atau default
            return ['status' => false, 'message' => 'WhatsApp notification failed'];

        } catch (\Exception $e) {
            // Handle error umum lainnya
            Log::error('❌ Wablas General Error: ' . $e->getMessage(), [
                'target' => $targetPhone,
                'error' => $e->getTrace(),
            ]);

            return ['status' => false, 'message' => 'WhatsApp notification failed'];
        }
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }
}