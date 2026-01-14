<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyRegistrationController;
use App\Http\Controllers\InvitationController;

// Change from this:
Route::get('/', function () {
    return view('welcome');
})->name('home');

// To this:
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Company registration
Route::get('/register/company', [CompanyRegistrationController::class, 'create'])
    ->name('company.register');
Route::post('/register/company', [CompanyRegistrationController::class, 'store']);

// Invitation acceptance
Route::get('/invitation/{token}', [InvitationController::class, 'accept'])
    ->name('invitation.accept');
Route::post('/invitation/{token}', [InvitationController::class, 'process'])
    ->name('invitation.process');
Route::get('/invitation/{token}/decline', [InvitationController::class, 'decline'])
    ->name('invitation.decline');

// Authentication routes (from Breeze)
require __DIR__.'/auth.php';

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Profile routes - redirect to company profile
    Route::get('/profile', function () {
        return redirect()->route('company.profile');
    })->name('profile.edit');
    
    Route::patch('/profile', function () {
        return redirect()->route('company.profile');
    })->name('profile.update');
    
    Route::delete('/profile', function () {
        return redirect()->route('company.profile');
    })->name('profile.destroy');

    // Redirect based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }
        
        return redirect()->route('company.dashboard');
    })->name('dashboard');

    // Super Admin routes
    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
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

    // Company routes
    Route::middleware(['role:company_admin,support_user,normal_user'])->prefix('company')->name('company.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Company\DashboardController::class, 'index'])
            ->name('dashboard');

        // Company Profile routes (ADD THESE - not the redirect ones)
        Route::get('/profile', [\App\Http\Controllers\Company\ProfileController::class, 'index'])
            ->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Company\ProfileController::class, 'update'])
            ->name('profile.update');
        
        // Subscription
        Route::get('/subscription', [\App\Http\Controllers\Company\SubscriptionController::class, 'index'])
            ->name('subscription');
        Route::post('/subscription', [\App\Http\Controllers\Company\SubscriptionController::class, 'subscribe'])
            ->name('subscription.subscribe');
        Route::post('/subscription/{subscription}/cancel', [\App\Http\Controllers\Company\SubscriptionController::class, 'cancel'])
            ->name('subscription.cancel');
        Route::get('/invoices', [\App\Http\Controllers\Company\SubscriptionController::class, 'invoices'])
            ->name('invoices');
        Route::get('/invoice/{payment}', [\App\Http\Controllers\Company\SubscriptionController::class, 'downloadInvoice'])
            ->name('invoice.download');
        
        // Team Management
        Route::get('/team', [\App\Http\Controllers\Company\TeamController::class, 'index'])
            ->name('team');
        Route::post('/team/invite', [\App\Http\Controllers\Company\TeamController::class, 'invite'])
            ->name('team.invite');
        Route::post('/team/invitation/{invitation}/resend', [\App\Http\Controllers\Company\TeamController::class, 'resendInvitation'])
            ->name('team.invitation.resend');
        Route::post('/team/invitation/{invitation}/revoke', [\App\Http\Controllers\Company\TeamController::class, 'revokeInvitation'])
            ->name('team.invitation.revoke');
        Route::post('/team/{user}/role', [\App\Http\Controllers\Company\TeamController::class, 'updateRole'])
            ->name('team.user.role');
        Route::delete('/team/{user}', [\App\Http\Controllers\Company\TeamController::class, 'removeUser'])
            ->name('team.user.remove');
        
        // File Management
        Route::get('/files', [\App\Http\Controllers\Company\FileController::class, 'index'])
            ->name('files');
        Route::post('/files', [\App\Http\Controllers\Company\FileController::class, 'store'])
            ->name('files.store');
        Route::get('/files/{file}', [\App\Http\Controllers\Company\FileController::class, 'show'])
            ->name('files.download');
        Route::delete('/files/{file}', [\App\Http\Controllers\Company\FileController::class, 'destroy'])
            ->name('files.destroy');
        Route::post('/files/{file}/toggle-visibility', [\App\Http\Controllers\Company\FileController::class, 'toggleVisibility'])
            ->name('files.toggle-visibility');
        
        // Support Messages
        Route::get('/support', [\App\Http\Controllers\Company\SupportMessageController::class, 'index'])
            ->name('support');
        Route::post('/support', [\App\Http\Controllers\Company\SupportMessageController::class, 'store'])
            ->name('support.store');
        Route::get('/support/{message}', [\App\Http\Controllers\Company\SupportMessageController::class, 'show'])
            ->name('support.show');
        Route::post('/support/{message}/reply', [\App\Http\Controllers\Company\SupportMessageController::class, 'reply'])
            ->name('support.reply');
        Route::post('/support/{message}/close', [\App\Http\Controllers\Company\SupportMessageController::class, 'close'])
            ->name('support.close');
        Route::post('/support/{message}/reopen', [\App\Http\Controllers\Company\SupportMessageController::class, 'reopen'])
            ->name('support.reopen');
        
        // Audit Logs
        Route::get('/audit-logs', [\App\Http\Controllers\Company\AuditLogController::class, 'index'])
            ->name('audit-logs');
        Route::get('/audit-logs/{auditLog}', [\App\Http\Controllers\Company\AuditLogController::class, 'show'])
            ->name('audit-logs.show');
        Route::get('/audit-logs/export', [\App\Http\Controllers\Company\AuditLogController::class, 'export'])
            ->name('audit-logs.export');
    });
});