<?php
// In routes/api/v1/CallReport.php

use App\Http\Controllers\Api\V1\CallReportController;
use Illuminate\Support\Facades\Route;

// --- N8N INTAKE ROUTE ---
// This route is for n8n. It is NOT protected by Sanctum.
// It is protected by our custom 'n8n.token' middleware.
Route::post(
    'call-reports/intake',
    [CallReportController::class, 'intake']
)->name('call-reports.intake');


// --- DASHBOARD "READ" ROUTES ---
// These routes are for your frontend dashboard.
// They ARE protected by Sanctum.
Route::middleware('auth:sanctum')->group(function () {

    // Get all reports (Super-Admin)
    Route::get(
        'call-reports',
        [CallReportController::class, 'index']
    )->name('call-reports.index');

    // Get a single report
    Route::get(
        'call-reports/{call_report}',
        [CallReportController::class, 'show']
    )->name('call-reports.show');

    // Get all reports for a specific company (Admin)
    Route::get(
        'companies/{company}/call-reports',
        [CallReportController::class, 'indexForCompany']
    )->name('companies.call-reports.index');

});
