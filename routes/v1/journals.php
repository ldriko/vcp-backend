<?php

use App\Http\Controllers\v1\JournalController;
use Illuminate\Support\Facades\Route;

Route::prefix('/journals')->group(function () {
    Route::get('/search', [JournalController::class, 'search']);
    Route::middleware('journal.published')->group(function () {
        Route::get('/{journal}', [JournalController::class, 'show']);
    });
    Route::get('/{journal}/pdf', [JournalController::class, 'showPdf'])
        ->middleware('journal.published');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('', [JournalController::class, 'index']);
        Route::post('', [JournalController::class, 'store']);

        Route::middleware('journal.author')->group(function () {
            Route::put('/{journal}', [JournalController::class, 'update']);
            Route::put('/{journal}/publish', [JournalController::class, 'publish']);
            Route::delete('/{journal}', [JournalController::class, 'destroy']);
        });
    });
});