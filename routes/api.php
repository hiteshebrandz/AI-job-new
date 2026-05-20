<?php

use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ResumeUploadController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return new UserResource($request->user());
    });

    // Resume parsing — accessible to any authenticated user
    Route::post('/resume/upload', [ResumeUploadController::class, 'upload']);
    Route::get('/resume/parse/{log}', [ResumeUploadController::class, 'status']);
    Route::post('/resume/profile', [ResumeUploadController::class, 'storeProfile']);

    // Analytics — candidates only
    Route::middleware('role:user')->group(function () {
        Route::get('/analytics/resume', [AnalyticsController::class, 'resumeData']);
    });
});
