<?php

namespace App\Http\Controllers;

use App\Services\HuggingFaceTTSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TTSController extends Controller
{
    protected $ttsService;

    public function __construct(HuggingFaceTTSService $ttsService)
    {
        $this->ttsService = $ttsService;
    }

    public function convertTextToSpeech(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:500',
        ]);

        $audio = $this->ttsService->synthesizeSpeech($request->text);

        return response($audio)
            ->header('Content-Type', 'audio/flac'); // Output format (adjust if needed)
    }

    // Optional: Save audio to storage
    public function saveSpeech(Request $request)
    {
        $audio = $this->ttsService->synthesizeSpeech($request->text);
        $filename = 'tts_' . time() . '.flac';
        Storage::disk('public')->put($filename, $audio);

        return response()->json([
            'url' => asset("storage/{$filename}"),
        ]);
    }
}