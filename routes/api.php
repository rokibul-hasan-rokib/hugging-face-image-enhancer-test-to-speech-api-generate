<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

Route::post('/tts', function (Request $request) {
    $request->validate([
        'text' => 'required|string|max:1000',
    ]);

    $text = $request->input('text');

    try {
        $response = Http::timeout(20)->post('http://127.0.0.1:5001/synthesize', [
            'text' => $text
        ]);

        if (!$response->ok()) {
            Log::error('TTS server error: ' . $response->body());
            return response()->json(['error' => 'TTS server error'], 500);
        }

        // Save file temporarily
        $fileName = 'speech_' . Str::random(8) . '.wav';
        $path = storage_path("app/public/$fileName");
        file_put_contents($path, $response->body());

        // Return as downloadable response
        return response()->download($path)->deleteFileAfterSend(true);

        // 👇 Optional: return base64 instead of file
        /*
        $base64 = base64_encode($response->body());
        return response()->json([
            'audio_base64' => $base64,
            'filename' => $fileName
        ]);
        */
    } catch (\Exception $e) {
        Log::error('TTS API call failed: ' . $e->getMessage());
        return response()->json(['error' => 'Internal Server Error'], 500);
    }
});
