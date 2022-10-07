<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\v1\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    Route::put('/user', [RegisteredUserController::class, 'update']);
});

Route::prefix('categories')->group(function () {
    Route::get('', [CategoryController::class, 'index']);
    Route::get('/{category}', [CategoryController::class, 'show']);
});

require __DIR__ . '/auth.php';
require __DIR__ . '/journals.php';
require __DIR__ . '/groups.php';

