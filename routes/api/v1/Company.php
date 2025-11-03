<?php
// In routes/api/v1/Company.php

use App\Http\Controllers\Api\V1\CompanyController;
use Illuminate\Support\Facades\Route;

// All routes here are automatically prefixed with /api/v1
// and protected by 'auth:sanctum' middleware.
Route::middleware('auth:sanctum')->group(function () {

    // This will create all the standard resourceful routes:
    // GET /api/v1/companies            (index)
    // POST /api/v1/companies           (store)
    // GET /api/v1/companies/{company}  (show)
    // PUT /api/v1/companies/{company}  (update)
    // DELETE /api/v1/companies/{company}(destroy)
    Route::apiResource('companies', CompanyController::class);

});
