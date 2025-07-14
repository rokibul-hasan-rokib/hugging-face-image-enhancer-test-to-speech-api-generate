<?php

use App\Http\Controllers\HuggingFaceController;
use App\Http\Controllers\TTSController;
use Illuminate\Support\Facades\Route;


Route::get('/', fn () => view('welcome'));

Route::get('/', fn () => view('welcome'));
Route::get('/result', fn () => view('result'));
Route::post('/enhance-image', [HuggingFaceController::class, 'enhanceUsingCodeFormer'])->name('enhance.image');

Route::get('/tts', [TTSController::class, 'index'])->name('tts.index');
Route::post('/tts/generate', [TTSController::class, 'generate'])->name('tts.generate');