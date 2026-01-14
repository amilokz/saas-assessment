<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])
        ->name('dashboard');
    
    // Companies
    Route::get('/companies', [\App\Http\Controllers\SuperAdmin\CompanyApprovalController::class, 'index'])
        ->name('companies.index');
    Route::post('/companies/{company}/approve', [\App\Http\Controllers\SuperAdmin\CompanyApprovalController::class, 'approve'])
        ->name('companies.approve');
    Route::post('/companies/{company}/reject', [\App\Http\Controllers\SuperAdmin\CompanyApprovalController::class, 'reject'])
        ->name('companies.reject');
    Route::post('/companies/{company}/suspend', [\App\Http\Controllers\SuperAdmin\CompanyApprovalController::class, 'suspend'])
        ->name('companies.suspend');
    Route::post('/companies/{company}/activate', [\App\Http\Controllers\SuperAdmin\CompanyApprovalController::class, 'activate'])
        ->name('companies.activate');
    
    // Plans
    Route::get('/plans', [\App\Http\Controllers\SuperAdmin\PlanController::class, 'index'])
        ->name('plans.index');
    Route::get('/plans/create', [\App\Http\Controllers\SuperAdmin\PlanController::class, 'create'])
        ->name('plans.create');
    Route::post('/plans', [\App\Http\Controllers\SuperAdmin\PlanController::class, 'store'])
        ->name('plans.store');
    Route::get('/plans/{plan}/edit', [\App\Http\Controllers\SuperAdmin\PlanController::class, 'edit'])
        ->name('plans.edit');
    Route::put('/plans/{plan}', [\App\Http\Controllers\SuperAdmin\PlanController::class, 'update'])
        ->name('plans.update');
    Route::delete('/plans/{plan}', [\App\Http\Controllers\SuperAdmin\PlanController::class, 'destroy'])
        ->name('plans.destroy');
    Route::post('/plans/{plan}/toggle-status', [\App\Http\Controllers\SuperAdmin\PlanController::class, 'toggleStatus'])
        ->name('plans.toggle-status');
});