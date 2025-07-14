<?php

namespace App\Http\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TTSService
{
    protected $client;
    protected $config;

    public function __construct()
    {
        $this->client = new Client();
        $this->config = config('huggingface');
    }

    public function generateSpeech($text)
    {
        try {
            $response = $this->client->post(
                $this->config['api_url'] . $this->config['tts_model'],
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->config['api_token'],
                        'Content-Type' => 'application/json',
                    ],
                    'json' => ['inputs' => $text],
                    'timeout' => 30
                ]
            );

            return [
                'success' => true,
                'audio' => base64_encode($response->getBody()->getContents()),
                'format' => 'wav'
            ];
        } catch (\Exception $e) {
            Log::error('TTS Generation Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}