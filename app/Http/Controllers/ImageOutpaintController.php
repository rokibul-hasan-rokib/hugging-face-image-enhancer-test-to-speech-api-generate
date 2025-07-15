<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImageOutpaintController extends Controller
{
    public function showForm()
    {
        return view('outpaint');
    }
    public function index()
    {
        return view('outpaint_result');
    }

    // public function process(Request $request)
    // {
    //     $request->validate([
    //         'image' => 'required|image|max:2048'
    //     ]);

    //     $imagePath = $request->file('image')->store('public/uploads');
    //     $imageFullPath = storage_path('app/' . $imagePath); // ঠিক path

    //     $imageContent = file_get_contents($imageFullPath);


    //     // API call to Hugging Face space
    //     $response = Http::withHeaders([
    //         'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_TOKEN')
    //     ])->attach(
    //         'image', $imageContent, 'input.jpg'
    //     )->post('https://fffiloni-diffusers-image-outpaint.hf.space/api/predict');

    //     if ($response->successful()) {
    //         $json = $response->json();
    //         $image_url = $json['data'][0]; // get result image URL

    //         return view('outpaint_result', compact('image_url'));
    //     }

    //     return back()->with('error', 'Image enhancement failed.');
    // }

    public function process(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        // Store the uploaded image correctly
        $imagePath = $request->file('image')->store('/uploads');
        $imageFullPath = storage_path('app/public/' . $imagePath);

        // Check if file exists before reading
        if (!file_exists($imageFullPath)) {
            return back()->with('error', 'Uploaded file not found.');
        }

        $imageContent = file_get_contents($imageFullPath);

        // // API call without prompt
        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_TOKEN')
        // ])->attach(
        //     'image', $imageContent, 'input.jpg'
        // )->post('https://fffiloni-diffusers-image-outpaint.hf.space');

        $imageBase64 = base64_encode($imageContent);

        $response = Http::timeout(60)->post('https://akhaliq-real-esrgan.hf.space/api/predict', [
            'data' => ["data:image/jpeg;base64," . $imageBase64]
        ]);

        if ($response->successful()) {
            $resultUrl = $response->json()['data'][0];
            return view('result', compact('resultUrl'));
        } else {
            dd($response->status(), $response->body());
        }


        // Token validation check
        if (!$response->successful()) {
            return back()->with('error', 'Invalid or expired token. Server says: ' . $response->status());
        }

        $json = $response->json();
        if (!isset($json['data'][0])) {
            return back()->with('error', 'Image enhancement failed. No output received.');
        }

        $image_url = $json['data'][0];
        return view('outpaint_result', compact('image_url'));
    }
}
