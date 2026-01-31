<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\API\VideoController;

// Default welcome page
Route::get('/', function () {
    return view('welcome');
});

// ✅ Video streaming route with signed URL validation
Route::get('/stream/{id}', [VideoController::class, 'stream'])
    ->name('video.stream');

// 🚀 Temporary route to run the seeder
Route::get('/run-seeder', function () {
    Artisan::call('db:seed');
    return 'Seeder executed!';
});


