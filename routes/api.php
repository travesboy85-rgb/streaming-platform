<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\VideoController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\SubscriptionPlanController;
use App\Http\Controllers\API\WatchHistoryController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\AdminController;

Route::prefix('v1')->middleware('api')->group(function () {

    // ✅ Test route
    Route::get('/test', function () {
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
        // User management (self)
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Video interactions
        Route::post('/videos', [VideoController::class, 'store']);
        Route::put('/videos/{video}', [VideoController::class, 'update']);
        Route::delete('/videos/{video}', [VideoController::class, 'destroy']);

        // Watch history
        Route::get('/watch-history', [WatchHistoryController::class, 'index']);
        Route::post('/videos/{video}/watch', [WatchHistoryController::class, 'store']);

        // Subscription
        Route::post('/subscribe/{plan}', [SubscriptionController::class, 'subscribe']);
    });

    // ✅ Admin-only routes
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        // User management
        Route::get('/users', [AuthController::class, 'allUsers']);
        Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);

        // Analytics
        Route::get('/analytics', [AdminController::class, 'analytics']);
    });
});

