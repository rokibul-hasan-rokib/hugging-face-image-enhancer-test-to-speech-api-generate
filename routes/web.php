<?php

use App\Http\Controllers\ApiImageFillController;
use App\Http\Controllers\HuggingFaceController;
use App\Http\Controllers\ImageEnhancerController;
use App\Http\Controllers\ImageOutpaintController;
use App\Http\Controllers\TTSController;
use Illuminate\Support\Facades\Route;


Route::get('/', fn () => view('welcome'));

Route::get('/', fn () => view('welcome'));
Route::get('/result', fn () => view('result'));
Route::post('/enhance-image', [HuggingFaceController::class, 'enhanceUsingCodeFormer'])->name('enhance.image');

Route::post('/tts', [TTSController::class, 'convertTextToSpeech']);
Route::post('/tts/save', [TTSController::class, 'saveSpeech']);

// Route::post('/tts', [HuggingFaceController::class, 'synthesizeSpeech'])->name('tts.speak');

Route::get('/image-enhance', [ImageEnhancerController::class, 'index']);
Route::post('/image-enhance', [ImageEnhancerController::class, 'enhance'])->name('enhance');


Route::get('/outpaint', [ImageOutpaintController::class, 'showForm']);
Route::get('/outpaints', [ImageOutpaintController::class, 'index']);
Route::post('/outpaint', [ImageOutpaintController::class, 'process'])->name('outpaint.process');

Route::get('/api/fill-image', [ApiImageFillController::class, 'fillImage']);
Route::post('/api/fill-image', [ApiImageFillController::class, 'fillImage']);

Route::get('/clear-result', [ApiImageFillController::class, 'clearResult']);
Route::post('/fill-image', [ApiImageFillController::class, 'fillImage']);

Route::view('/test', 'image-fill');
