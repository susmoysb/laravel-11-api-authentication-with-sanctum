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
        Route::post('/logout/{tokenId?}', 'logout')->where('tokenId', '[0-9]+');
        Route::get('/login-sessions', 'loginSessions');

        Route::get('users/me', [UserController::class, 'me']);
        Route::patch('users/change-password', [UserController::class, 'changePassword']);
        Route::apiResource('users', UserController::class);
    });
});
