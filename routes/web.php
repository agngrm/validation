<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardValidatorController;
use App\Http\Controllers\SqlInjectionDemoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Add these new routes for the validator example
    Route::get('/dashboard/validator', [DashboardValidatorController::class, 'index'])
        ->name('dashboard.validator');

    Route::post('/dashboard/validator/process', [DashboardValidatorController::class, 'process'])
        ->name('dashboard.validator.process');

    // SQL Injection Prevention Demo
    Route::get('/dashboard/sql-injection', [SqlInjectionDemoController::class, 'index'])
        ->name('sql.injection');

    Route::post('/dashboard/sql-injection/vulnerable', [SqlInjectionDemoController::class, 'vulnerable'])
        ->name('sql.vulnerable');

    Route::post('/dashboard/sql-injection/secure-bind', [SqlInjectionDemoController::class, 'secureBind'])
        ->name('sql.secure.bind');

    Route::post('/dashboard/sql-injection/secure-builder', [SqlInjectionDemoController::class, 'secureQueryBuilder'])
        ->name('sql.secure.builder');

    Route::post('/dashboard/sql-injection/secure-eloquent', [SqlInjectionDemoController::class, 'secureEloquent'])
        ->name('sql.secure.eloquent');

    Route::post('/dashboard/sql-injection/secure-validation', [SqlInjectionDemoController::class, 'secureValidation'])
        ->name('sql.secure.validation');
});

require __DIR__.'/auth.php';
