<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{
    public function sendToItSupport($targetPhone, $receiverName, $location, $reporterName, $description, $link)
    {
        $message = "📢 *Laporan Baru Masuk!*\n\n"
                 . "👤 Pelapor: {$reporterName}\n"
                 . "🏢 Lokasi: {$location}\n"
                 . "📝 Masalah: {$description}\n\n"
                 . "🔗 Lihat Laporan: {$link}";

        $response = Http::withHeaders([
            'Authorization' => env('WABLAS_API_KEY'),
        ])->asForm()->post('https://console.wablas.com/api/v2/send-message', [
            'device' => env('WABLAS_DEVICE'),
            'phone' => $this->formatPhone($targetPhone),
            'message' => $message,
            'priority' => true,
        ]);

        return $response->json();
    }

    private function formatPhone($phone)
    {
        // Bersihkan karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Ubah 08xxx ke 628xxx
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }
}
