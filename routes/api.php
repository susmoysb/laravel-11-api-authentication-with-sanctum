<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login-sessions', 'loginSessions');
        Route::post('/logout/{tokenId?}', 'logout')->where('tokenId', '[0-9]+');
    });

    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/me', 'me');
        Route::patch('/change-password', 'changePassword');
    });
    Route::apiResource('users', UserController::class);
});
