<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\v1\GroupChatController;
use App\Http\Controllers\v1\GroupController;
use App\Http\Controllers\v1\GroupMemberController;
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
            Route::delete('/{journal}', [JournalController::class, 'destroy']);
        });
    });

    Route::prefix('/groups')->group(function () {
        Route::get('', [GroupController::class, 'index']);
        Route::post('', [GroupController::class, 'store']);
        Route::get('/{group}/invite', [GroupController::class, 'invite'])
            ->middleware('group.admin');
        Route::post('/{group}/join', [GroupController::class, 'join'])
            ->middleware('signed')
            ->name('group.join');

        Route::prefix('/{group}')->middleware('group.member')->group(function () {
            Route::get('', [GroupController::class, 'show']);

            Route::middleware('group.admin')->group(function () {
                Route::put('', [GroupController::class, 'update']);
                Route::delete('', [GroupController::class, 'destroy']);
            });

            Route::prefix('/members')->middleware('group.admin')->group(function () {
                Route::get('', [GroupMemberController::class, 'index']);
                Route::delete('/{user}', [GroupMemberController::class, 'destroy']);
            });

            Route::prefix('/chat')->group(function () {
                Route::post('', [GroupChatController::class, 'store']);
            });
        });
    });
});
