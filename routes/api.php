<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Company Registration API
Route::post('/register/company', [\App\Http\Controllers\CompanyRegistrationController::class, 'store']);

// Protected API routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Company endpoints
    Route::apiResource('companies', \App\Http\Controllers\Api\CompanyController::class)
        ->only(['show', 'update']);
    
    // Subscription endpoints
    Route::apiResource('subscriptions', \App\Http\Controllers\Api\SubscriptionController::class);
    
    // Team endpoints
    Route::apiResource('team', \App\Http\Controllers\Api\TeamController::class);
    
    // File endpoints
    Route::apiResource('files', \App\Http\Controllers\Api\FileController::class);
    
    // Support endpoints
    Route::apiResource('support', \App\Http\Controllers\Api\SupportController::class);
});

// Public invitation acceptance API
Route::post('/invitations/{token}/accept', [\App\Http\Controllers\InvitationController::class, 'processApi']);