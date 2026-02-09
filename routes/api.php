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
            'status'  => 'online'
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
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);

        // Watch history
        Route::get('/watch-history', [WatchHistoryController::class, 'index']);
        Route::post('/videos/{video}/watch', [WatchHistoryController::class, 'store']);

        // Subscription
        Route::post('/subscribe/{plan}', [SubscriptionController::class, 'subscribe']);

        // ✅ User: like video
        Route::post('/videos/{id}/like', [VideoController::class, 'like']);
    });

    // ✅ Creator-only routes
    Route::middleware(['auth:sanctum', 'role:creator'])->group(function () {
        Route::post('/videos/upload', [VideoController::class, 'upload']);   // Creator upload
        Route::get('/videos/mine', [VideoController::class, 'mine']);       // Creator’s own videos
    });

    // ✅ Admin-only routes
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/users', [AuthController::class, 'allUsers']);
        Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);
        Route::get('/analytics', [AdminController::class, 'analytics']);

        // Admin video moderation
        Route::get('/videos/pending', [VideoController::class, 'pending']);
        Route::post('/videos/{id}/approve', [VideoController::class, 'approve']);
        Route::delete('/videos/{id}', [VideoController::class, 'destroy']); // ✅ delete video
    });
});


