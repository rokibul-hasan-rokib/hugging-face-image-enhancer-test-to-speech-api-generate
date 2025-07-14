<?php

// app/Http/Controllers/ImageEnhancerController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HuggingFaceController extends Controller
{
    public function enhanceUsingCodeFormer(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:4096|mimes:jpg,jpeg,png',
        ]);

        try {
            $image = $request->file('image');
            $imageData = base64_encode(file_get_contents($image->getRealPath()));

            // Use a verified working model
            $modelEndpoint = 'https://api-inference.huggingface.co/models/TencentARC/GFPGAN';
            // Alternative: 'https://api-inference.huggingface.co/models/ai-forever/Real-ESRGAN'

            $response = Http::withToken(env('HUGGINGFACE_API_TOKEN'))
                ->timeout(120)
                ->withHeaders([
                    'Accept' => 'image/png',
                    'Content-Type' => 'application/json',
                ])
                ->post($modelEndpoint, [
                    'inputs' => $imageData,
                    'parameters' => [
                        'scale' => 2 // Enhancement scale factor
                    ],
                    'options' => [
                        'wait_for_model' => true
                    ]
                ]);

            if ($response->successful()) {
                return response($response->body())
                    ->header('Content-Type', 'image/png');
            }

            $errorDetails = $response->json();
            $errorMessage = $errorDetails['error'] ?? $response->body();

            Log::error('HuggingFace API Error', [
                'status' => $response->status(),
                'error' => $errorMessage,
                'endpoint' => $modelEndpoint
            ]);

            throw new \Exception($errorMessage);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Enhancement failed',
                'message' => $e->getMessage(),
                'solution' => [
                    '1. Try a different image (JPEG format works best)',
                    '2. Reduce image size below 2MB',
                    '3. Verify your API token has sufficient credits',
                    '4. Try again in a few minutes if model is loading'
                ]
            ], 500);
        }
}
}
