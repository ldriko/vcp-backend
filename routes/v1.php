<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\v1\JournalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    Route::put('/user', [RegisteredUserController::class, 'update']);

    Route::prefix('/journals')->group(function () {
        Route::get('', [JournalController::class, 'index']);
        Route::get('/{journal}', [JournalController::class, 'show']);
        Route::post('', [JournalController::class, 'store']);
        Route::put('/{journal}', [JournalController::class, 'update']);
    });
});
