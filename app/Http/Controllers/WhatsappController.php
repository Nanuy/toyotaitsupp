<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{
    public function sendToItSupport($to, $name, $location, $reporter, $description, $link)
    {
        $token = env('WHATSAPP_TOKEN'); // Simpan token di .env
        $phoneNumberId = env('WHATSAPP_PHONE_ID'); // ID dari Meta
        $url = "https://graph.facebook.com/v17.0/{$phoneNumberId}/messages";

        $response = Http::withToken($token)->post($url, [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => 'nama_template_kamu', // Ganti sesuai template yang di-approve
                'language' => ['code' => 'id'],
                'components' => [[
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => $name],
                        ['type' => 'text', 'text' => $location],
                        ['type' => 'text', 'text' => $reporter],
                        ['type' => 'text', 'text' => $description],
                        ['type' => 'text', 'text' => $link],
                    ]
                ]]
            ]
        ]);

        return $response->json();
    }
}

?>