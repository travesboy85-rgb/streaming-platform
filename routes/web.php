<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\VideoController;

// Default welcome page
Route::get('/', function () {
    return view('welcome');
});

// ✅ Video streaming route with signed URL validation
Route::get('/stream/{id}', [VideoController::class, 'stream'])
    ->name('video.stream');

