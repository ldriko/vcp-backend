<?php

use App\Http\Controllers\v1\GroupAttachmentController;
use App\Http\Controllers\v1\GroupChatController;
use App\Http\Controllers\v1\GroupController;
use App\Http\Controllers\v1\GroupMemberController;
use Illuminate\Support\Facades\Route;

Route::prefix('/groups')
    ->middleware('auth:sanctum')
    ->group(function () {
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
                Route::get('', [GroupChatController::class, 'index']);
                Route::post('', [GroupChatController::class, 'store']);
            });

            Route::prefix('/attachments')->group(function () {
                Route::get('', [GroupAttachmentController::class, 'index']);
                Route::post('', [GroupAttachmentController::class, 'store']);
            });
        });
    });