<?php
// In routes/api/v1/Role.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\RoleController;

// All routes in this file are protected by Sanctum and your 'manage-roles' permission
Route::middleware(['auth:sanctum', 'can:manage-roles'])
    ->group(function () {

        // Routes to list roles or show a single role (and its permissions)
        // GET /api/v1/roles
        // GET /api/v1/roles/{role}
        Route::apiResource('roles', RoleController::class)->only(['index', 'show']);

        // Route to assign a permission to a role
        // POST /api/v1/roles/{role}/permissions
        Route::post('roles/{role}/permissions', [
            RoleController::class, 'assignPermission'
        ])->name('roles.permissions.assign');

        // Route to revoke a permission from a role
        // DELETE /api/v1/roles/{role}/permissions
        Route::delete('roles/{role}/permissions', [
            RoleController::class, 'revokePermission'
        ])->name('roles.permissions.revoke');

    });
