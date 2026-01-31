<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\VideoController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\SubscriptionPlanController;
use App\Http\Controllers\API\WatchHistoryController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\AdminController;

Route::prefix('v1')->group(function () {

    // ✅ Health check route
    Route::get('/health', function () {
        return response()->json([
            'status'  => 'ok',
            'message' => 'Service is healthy',
            'time'    => now()->toDateTimeString()
        ]);
    });

    // ✅ Test route
    Route::get('/test', function () {
        \Log::info('Test route hit');
        return response()->json([
            'message' => 'Streaming Platform API is working!',
            'version' => '1.0',
            'status' => 'online'
        ]);
    });

    // ✅ Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    // Videos (public view only)
    Route::get('/videos', [VideoController::class, 'index']);
    Route::get('/videos/{video}', [VideoController::class, 'show']);

    // Subscription Plans
    Route::get('/plans', [SubscriptionPlanController::class, 'index']);

    // ✅ Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/videos', [VideoController::class, 'store']);
        Route::put('/videos/{video}', [VideoController::class, 'update']);
        Route::delete('/videos/{video}', [VideoController::class, 'destroy']);
        Route::get('/watch-history', [WatchHistoryController::class, 'index']);
        Route::post('/videos/{video}/watch', [WatchHistoryController::class, 'store']);
        Route::post('/subscribe/{plan}', [SubscriptionController::class, 'subscribe']);
    });

    // ✅ Admin-only routes
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/users', [AuthController::class, 'allUsers']);
        Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);
        Route::get('/analytics', [AdminController::class, 'analytics']);
    });
});



