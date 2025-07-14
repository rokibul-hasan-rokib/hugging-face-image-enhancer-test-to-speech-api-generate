<?php

namespace App\Http\Controllers;

use App\Http\Services\TTSService;
use Illuminate\Http\Request;

class TTSController extends Controller
{
    protected $ttsService;

    public function __construct(TTSService $ttsService)
    {
        $this->ttsService = $ttsService;
    }

    public function index()
    {
        return view('tts.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:500'
        ]);

        $result = $this->ttsService->generateSpeech($request->text);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'audio' => $result['audio'],
                'format' => $result['format']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 500);
    }
}