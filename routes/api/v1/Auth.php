<?php
// In routes/api/v1/Auth.php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

// This route will become: /api/v1/login
Route::post('/login', [AuthController::class, 'login'])->name('login');

// This route will become: /api/v1/user
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

