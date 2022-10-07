<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\v1\GroupAttachmentController;
use App\Http\Controllers\v1\GroupChatController;
use App\Http\Controllers\v1\GroupController;
use App\Http\Controllers\v1\GroupMemberController;
use App\Http\Controllers\v1\JournalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';
require __DIR__ . '/journals.php';
require __DIR__ . '/groups.php';

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    Route::put('/user', [RegisteredUserController::class, 'update']);
});
