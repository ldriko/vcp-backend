<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\v1\JournalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::get('/journals/{journal}/pdf', [JournalController::class, 'showPdf'])
    ->middleware('journal.published');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    Route::put('/user', [RegisteredUserController::class, 'update']);

    Route::prefix('/journals')->group(function () {
        Route::get('', [JournalController::class, 'index']);
        Route::post('', [JournalController::class, 'store']);

        Route::middleware('journal.published')->group(function () {
            Route::get('/{journal}', [JournalController::class, 'show']);
        });

        Route::middleware('journal.author')->group(function () {
            Route::put('/{journal}', [JournalController::class, 'update']);
            Route::put('/{journal}/publish', [JournalController::class, 'publish']);
        });
    });
});
