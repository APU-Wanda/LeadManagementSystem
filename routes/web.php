<?php
declare(strict_types=1);

use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadImportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {

    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::put('/leads/{id}', [LeadController::class, 'update'])->name('leads.update');


    Route::get('/import', [LeadImportController::class, 'showImportForm'])->name('leads.import.form');
    Route::post('/import', [LeadImportController::class, 'import'])->name('leads.import');
    Route::get('/export', [LeadController::class, 'export'])->name('leads.export');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin-Only Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::delete('/leads/{id}', [LeadController::class, 'destroy'])->name('leads.destroy');
});

require __DIR__.'/auth.php';

