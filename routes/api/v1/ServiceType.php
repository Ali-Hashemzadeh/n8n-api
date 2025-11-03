<?php
// In routes/api/v1/ServiceType.php

use App\Http\Controllers\Api\V1\ServiceTypeController;
use Illuminate\Support\Facades\Route;

// All routes here are automatically prefixed with /api/v1
// and protected by 'auth:sanctum' middleware.
Route::middleware('auth:sanctum')->group(function () {

    // --- Routes Nested Under Company ---

    // GET /api/v1/companies/{company}/service-types
    // Get all service types for a specific company
    Route::get(
        'companies/{company}/service-types',
        [ServiceTypeController::class, 'index']
    )->name('companies.service-types.index');

    // POST /api/v1/companies/{company}/service-types
    // Create a new service type for a specific company
    Route::post(
        'companies/{company}/service-types',
        [ServiceTypeController::class, 'store']
    )->name('companies.service-types.store');


    // --- Routes for Specific Service Types ---
    // We use top-level routes for managing a specific resource
    // as we already have its unique ID.

    // GET /api/v1/service-types/{service_type}
    // Show a single service type
    Route::get(
        'service-types/{service_type}',
        [ServiceTypeController::class, 'show']
    )->name('service-types.show');

    // PUT /api/v1/service-types/{service_type}
    // Update a single service type
    Route::put(
        'service-types/{service_type}',
        [ServiceTypeController::class, 'update']
    )->name('service-types.update');

    // DELETE /api/v1/service-types/{service_type}
    // Delete a single service type
    Route::delete(
        'service-types/{service_type}',
        [ServiceTypeController::class, 'destroy']
    )->name('service-types.destroy');

});
