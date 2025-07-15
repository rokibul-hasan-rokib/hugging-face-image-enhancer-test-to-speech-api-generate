<?php

// app/Http/Controllers/ImageEnhancerController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\TextToSpeech\V1\Client\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\SynthesizeSpeechRequest;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;

class HuggingFaceController extends Controller
{
    public function enhanceUsingCodeFormer(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $image = $request->file('image');
        $imageData = file_get_contents($image);

        $url = 'https://api-inference.huggingface.co/models/nateraw/real-esrgan';
        $token = env('HUGGINGFACE_API_TOKEN');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/octet-stream',
        ])->withBody($imageData, 'application/octet-stream')
            ->post($url);

        if ($response->successful()) {
            $enhancedImagePath = 'images/enhanced_' . time() . '.png';
            file_put_contents(public_path($enhancedImagePath), $response->body());

            // Return the same view as form, with enhancedImage
            return view('enhance-form', [
                'enhancedImage' => asset($enhancedImagePath)
            ]);
        }
        if (!$response->successful()) {
            dd('API call failed', $response->status(), $response->body());
        }

        // For debugging, dump content type
        dd($response->header('Content-Type'), $response->body());

        return back()->withErrors(['error' => 'Enhancement failed']);
    }


    public function synthesizeSpeech(Request $request)
    {
        // Set credentials path from .env
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . base_path(env('GOOGLE_APPLICATION_CREDENTIALS')));

        // Validate input
        $request->validate([
            'text' => 'required|string',
        ]);

        // Initialize the client
        $client = new TextToSpeechClient();

        // Build request components
        $input = new SynthesisInput([
            'text' => $request->text,
        ]);

        $voice = new VoiceSelectionParams([
            'language_code' => 'en-US',
            'ssml_gender' => SsmlVoiceGender::FEMALE,
        ]);

        $audioConfig = new AudioConfig([
            'audio_encoding' => AudioEncoding::MP3,
        ]);

        // Combine into one request
        $ttsRequest = new SynthesizeSpeechRequest([
            'input' => $input,
            'voice' => $voice,
            'audio_config' => $audioConfig,
        ]);

        // Call the API
        $response = $client->synthesizeSpeech($ttsRequest);

        // Save audio to file
        $outputPath = storage_path('app/public/output.mp3');
        file_put_contents($outputPath, $response->getAudioContent());

        // Close client
        $client->close();

        // Return download response
        return response()->download($outputPath, 'speech.mp3');
    }
}
