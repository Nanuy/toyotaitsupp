<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $accessToken;
    protected $phoneNumberId;

    public function __construct()
    {
        $this->accessToken = env('WA_TOKEN'); // Simpan token di .env
        $this->phoneNumberId = env('WA_PHONE_ID'); // Simpan ID nomor WhatsApp
    }

    public function sendText($to, $message)
    {
        $url = "https://graph.facebook.com/v17.0/{$this->phoneNumberId}/messages";

        $response = Http::withToken($this->accessToken)
            ->post($url, [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => ['body' => $message]
            ]);

        return $response->json();
    }

    public function sendTemplate($to, $templateName = 'hello_world', $lang = 'en_US')
    {
        $url = "https://graph.facebook.com/v17.0/{$this->phoneNumberId}/messages";

        $response = Http::withToken($this->accessToken)
            ->post($url, [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => ['code' => $lang]
                ]
            ]);

        return $response->json();
    }
}
