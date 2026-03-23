<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavedViewController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

// Public Auth routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (Requires Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user',   [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/stats', [StatsController::class, 'index']);

    // CSV Import Pipeline (async — dispatches to Redis queue)
    Route::post('/import/upload', [ImportController::class, 'upload']);

    Route::prefix('report')->group(function () {
        Route::get('/fields',  [ReportController::class, 'fields']);
        Route::get('/data',    [ReportController::class, 'data']);
        Route::get('/facets',  [ReportController::class, 'facets']);
        Route::get('/compare', [ReportController::class, 'compare']);
        Route::get('/export',  [ReportController::class, 'export']);
    });

    Route::prefix('views')->group(function () {
        Route::get('/',        [SavedViewController::class, 'index']);
        Route::post('/',       [SavedViewController::class, 'store']);
        Route::delete('/{id}', [SavedViewController::class, 'destroy']);
    });
});