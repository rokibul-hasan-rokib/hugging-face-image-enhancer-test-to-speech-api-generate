<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HuggingFaceTTSService
{
    protected $client;
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api-inference.huggingface.co',
            'headers' => [
                'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
                'Content-Type' => 'application/json',
            ],
        ]);
        $this->model = "facebook/fastspeech2-en-ljspeech"; // Model ID
    }

    /**
     * Convert text to speech using FastSpeech2.
     *
     * @param string $text
     * @return mixed Audio content (binary)
     */
    public function synthesizeSpeech($text)
    {
        try {
            $response = $this->client->post("/models/{$this->model}", [
                'json' => ['inputs' => $text],
            ]);
            return $response->getBody();
        } catch (RequestException $e) {
            throw new \Exception("TTS API Error: " . $e->getMessage());
        }
    }
}