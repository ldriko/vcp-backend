<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\v1\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    Route::get('/user/picture', function (Request $request) {
        if (!$request->user()->picture_path) return response()->noContent();

        return Storage::disk('users')->download(
            $request->user()->picture_path,
            null,
            [
                'Content-Disposition' => $request->boolean('is_download') ? 'attachment;' : 'inline;'
            ]
        );
    });
    Route::put('/user', [RegisteredUserController::class, 'update']);
});

Route::prefix('categories')->group(function () {
    Route::get('', [CategoryController::class, 'index']);
    Route::get('/{category}', [CategoryController::class, 'show']);
});

require __DIR__ . '/auth.php';
require __DIR__ . '/journals.php';
require __DIR__ . '/groups.php';

