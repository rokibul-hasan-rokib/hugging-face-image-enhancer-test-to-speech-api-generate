<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ApiImageFillController extends Controller
{
    public function clearResult()
    {
        // First POST request to get EVENT_ID
        $response = Http::post('https://ozzygt-diffusers-image-fill.hf.space/call/clear_result', [
            'data' => []
        ]);

        $eventId = $response->json()[4]; // Extract EVENT_ID

        // Second GET request to get results
        $result = Http::get("https://ozzygt-diffusers-image-fill.hf.space/call/clear_result/{$eventId}");

        return $result->json();
    }

    public function fillImage(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'model' => 'required|string'
        ]);

        // Store the uploaded file temporarily
        $path = $request->file('image')->store('temp');
        $fullPath = storage_path('app/'.$path);

        $data = [
            'data' => [
                [
                    'background' => [
                        'path' => $fullPath,
                        'url' => asset("storage/{$path}"),
                        'orig_name' => $request->file('image')->getClientOriginalName(),
                        'is_file' => true,
                        'meta' => null
                    ],
                    'layers' => [],
                    'composite' => null
                ],
                $validated['model']
            ]
        ];

        // First POST request to get EVENT_ID
        $response = Http::post(
            'https://ozzygt-diffusers-image-fill.hf.space/call/fill_image',
            $data
        );

        // Debug the response if needed
        // logger()->info('API Response:', $response->json());

        // Properly extract EVENT_ID from the response
        $responseData = $response->json();
        $eventId = $responseData['event_id'] ??
                   $responseData['event-id'] ??
                   $responseData[0]['event_id'] ??
                   null;

        if (!$eventId) {
            Storage::delete($path);
            return response()->json([
                'error' => 'Failed to get EVENT_ID from API',
                'response' => $responseData
            ], 500);
        }

        // Second GET request to get results
        $result = Http::get("https://ozzygt-diffusers-image-fill.hf.space/call/fill_image/{$eventId}");

        dd($result);
        // Clean up: delete the temporary file
        Storage::delete($path);

        return $result->json();
    }
}
