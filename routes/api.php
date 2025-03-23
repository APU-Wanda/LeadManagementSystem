<?php
declare(strict_types=1);

use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadImportController;
use Illuminate\Support\Facades\Route;

Route::get('/leads', [LeadController::class, 'index']);
Route::post('/import', [LeadImportController::class, 'import'])->name('leads.import');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/import-auth', [LeadImportController::class, 'import']);
    Route::put('/leads/{id}', [LeadController::class, 'update']);
    Route::delete('/leads/{id}', [LeadController::class, 'destroy']);
    Route::get('/export', [LeadController::class, 'export']);
    Route::get('/dashboard', [LeadController::class, 'dashboard']);
});

Route::post('/import', [LeadImportController::class, 'import']);

