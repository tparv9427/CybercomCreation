<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavedViewController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

Route::get('/stats', [StatsController::class, 'index']);

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