<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TTSController extends Controller
{
    public function synthesize(Request $request)
    {
        $text = $request->input('text');

        // POST request পাঠানো TTS API তে
        $response = Http::post('http://127.0.0.1:5001/synthesize', [
            'text' => $text
        ]);

        if ($response->successful()) {
            // সার্ভার থেকে অডিও ফাইল রিসিভ
            $audioContent = $response->body();
            $filename = 'tts_output_' . time() . '.wav';
            $path = storage_path("app/public/tts/$filename");

            file_put_contents($path, $audioContent);

            return response()->download($path);
        } else {
            return response()->json([
                'error' => 'TTS সেবা ব্যর্থ হয়েছে',
                'status' => $response->status()
            ], 500);
        }
    }
}