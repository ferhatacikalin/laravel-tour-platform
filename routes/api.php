<?php

use App\Api\V1\Controllers\AuthController;
use App\Api\V1\Controllers\TourController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    // Auth routes
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);

    // Public tour routes
    Route::get('tours', [TourController::class, 'index']);
    Route::get('tours/{id}', [TourController::class, 'show']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::post('auth/me', [AuthController::class, 'me']);

        // Protected tour routes
        Route::post('tours', [TourController::class, 'store']);
        Route::put('tours/{id}', [TourController::class, 'update']);
        Route::delete('tours/{id}', [TourController::class, 'destroy']);
    });
});

