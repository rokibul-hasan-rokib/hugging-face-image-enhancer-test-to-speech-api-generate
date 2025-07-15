<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImageEnhancerController extends Controller
{
    public function index()
    {
        return view('image-enhance');
    }

    public function enhance(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:4096', // max 4MB
        ]);

        $image = $request->file('image');
        $imagePath = $image->getPathname();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
            ])->attach(
                'image', file_get_contents($imagePath), $image->getClientOriginalName()
            )->timeout(120)->post('https://api-inference.huggingface.co/models/cointegrated/Real-ESRGAN');
            dd($response);

            if (str_starts_with($response->header('content-type'), 'image')) {
                $enhancedImage = $response->body();
                $fileName = uniqid('enhanced_') . '.png';
                $filePath = public_path('images/' . $fileName);
                file_put_contents($filePath, $enhancedImage);

                return view('image-enhancer-form', [
                    'enhanced' => asset('images/' . $fileName),
                ]);
            }

            return back()->withErrors(['error' => 'Unexpected response', 'details' => $response->json()]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}