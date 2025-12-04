<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CustomerController;

// All routes here are automatically prefixed with /api/v1
// and protected by 'auth:sanctum' middleware via the RouteServiceProvider or explicit grouping

Route::middleware(['auth:sanctum'])->group(function () {

    // Standard CRUD routes for Customers
    // GET /api/v1/customers
    // POST /api/v1/customers
    // GET /api/v1/customers/{customer}
    // PUT /api/v1/customers/{customer}
    // DELETE /api/v1/customers/{customer}
    Route::apiResource('customers', CustomerController::class);

});
