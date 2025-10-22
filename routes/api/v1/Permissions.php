<?php
// In routes/api/v1/Permission.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PermissionController;

// All routes in this file are protected by Sanctum and your 'manage-perms' permission
Route::middleware(['auth:sanctum', 'can:manage-perms'])
    ->group(function () {

        // This will create:
        // GET /api/v1/permissions (to list all available permissions)
        Route::apiResource('permissions', PermissionController::class)
            ->only(['index']);
    });
