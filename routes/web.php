<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyRegistrationController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Company\ProfileController as CompanyProfileController;

// ========================
// PUBLIC ROUTES
// ========================

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }
        
        return redirect()->route('company.dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Company registration (PUBLIC - no auth required)
Route::get('/register/company', [CompanyRegistrationController::class, 'create'])
    ->name('company.register.form')
    ->middleware('guest');

Route::post('/register/company', [CompanyRegistrationController::class, 'store'])
    ->name('company.register.store')
    ->middleware('guest');

// Invitation acceptance (PUBLIC - no auth required for new users)
Route::get('/invitation/{token}', [InvitationController::class, 'accept'])
    ->name('invitation.accept');

Route::post('/invitation/{token}', [InvitationController::class, 'process'])
    ->name('invitation.process');

Route::get('/invitation/{token}/decline', [InvitationController::class, 'decline'])
    ->name('invitation.decline');

// ========================
// AUTHENTICATION ROUTES
// ========================

require __DIR__.'/auth.php';

// ========================
// PROTECTED ROUTES
// ========================

Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard redirect based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }
        
        // Check company status before allowing access
        if ($user->company) {
            if ($user->company->status === 'rejected') {
                auth()->logout();
                return redirect()->route('login')->with('error', 'Your company has been rejected.');
            }
            
            if ($user->company->status === 'suspended') {
                auth()->logout();
                return redirect()->route('login')->with('error', 'Your company is suspended.');
            }
        }
        
        return redirect()->route('company.dashboard');
    })->name('dashboard');

    // Trial status page
    Route::get('/company/trial-status', function() {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        $company = $user->company;
        
        if (!$company) {
            return redirect()->route('company.dashboard');
        }
        
        if ($company->status !== 'trial_pending_approval') {
            return redirect()->route('company.dashboard');
        }
        
        $trialDaysLeft = $company->trial_ends_at ? now()->diffInDays($company->trial_ends_at, false) : 0;
        $trialDaysLeft = max(0, $trialDaysLeft);
        
        return view('company.trial-status', compact('company', 'trialDaysLeft'));
    })->name('company.trial-status');

    // ========================
    // SUPER ADMIN ROUTES
    // ========================
    
    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])
            ->name('dashboard');
        
        // Companies Management
        Route::get('/companies', [\App\Http\Controllers\SuperAdmin\CompanyApprovalController::class, 'index'])
            ->name('companies.index');
        
        // Company detail view
        Route::get('/companies/{company}', [\App\Http\Controllers\SuperAdmin\CompanyApprovalController::class, 'show'])
            ->name('companies.show');
        
        // Company search
        Route::get('/companies/search', [\App\Http\Controllers\SuperAdmin\CompanyApprovalController::class, 'search'])
            ->name('companies.search');
        
        // ✅ ADD THIS LINE: Bulk actions for companies
        Route::post('/companies/bulk-action', [\App\Http\Controllers\SuperAdmin\CompanyApprovalController::class, 'bulkAction'])
            ->name('companies.bulk-action');
        
        // Company Actions
        Route::post('/companies/{company}/approve', [\App\Http\Controllers\SuperAdmin\CompanyApprovalController::class, 'approve'])
            ->name('companies.approve');
        
        Route::post('/companies/{company}/reject', [\App\Http\Controllers\SuperAdmin\CompanyApprovalController::class, 'reject'])
            ->name('companies.reject');
        
        Route::post('/companies/{company}/suspend', [\App\Http\Controllers\SuperAdmin\CompanyApprovalController::class, 'suspend'])
            ->name('companies.suspend');
        
        Route::post('/companies/{company}/activate', [\App\Http\Controllers\SuperAdmin\CompanyApprovalController::class, 'activate'])
            ->name('companies.activate');
        
        // Plans Management
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
        
        // Platform-wide Audit Logs
        Route::get('/audit-logs', [\App\Http\Controllers\SuperAdmin\AuditLogController::class, 'index'])
            ->name('audit-logs.index');
        
        Route::get('/audit-logs/{auditLog}', [\App\Http\Controllers\SuperAdmin\AuditLogController::class, 'show'])
            ->name('audit-logs.show');
    });

    // ========================
    // COMPANY USER ROUTES
    // ========================
    
    Route::middleware(['auth', 'check.company.status'])->prefix('company')->name('company.')->group(function () {
        
        // ========================
        // DASHBOARD & PROFILE
        // ========================
        
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Company\DashboardController::class, 'index'])
            ->name('dashboard');
        
        // Company Profile
        Route::get('/profile', [CompanyProfileController::class, 'show'])
            ->name('profile');
        
        Route::get('/profile/edit', [CompanyProfileController::class, 'edit'])
            ->name('profile.edit');
        
        Route::post('/profile/update', [CompanyProfileController::class, 'update'])
            ->name('profile.update');
        
        // ========================
        // SUBSCRIPTION (Admin Only)
        // ========================
        
        Route::middleware(['role:company_admin'])->group(function () {
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
        });
        
        // ========================
        // TEAM MANAGEMENT (Admin Only)
        // ========================
        
        Route::middleware(['role:company_admin'])->group(function () {
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
        });
        
        // ========================
        // FILE MANAGEMENT
        // ========================
        
        // View files (all company users)
        Route::get('/files', [\App\Http\Controllers\Company\FileController::class, 'index'])
            ->name('files');
        
        Route::get('/files/{file}', [\App\Http\Controllers\Company\FileController::class, 'show'])
            ->name('files.download');
        
        // Upload/Delete files (admin & support only)
        Route::middleware(['role:company_admin,support_user'])->group(function () {
            Route::post('/files', [\App\Http\Controllers\Company\FileController::class, 'store'])
                ->name('files.store');
            
            Route::delete('/files/{file}', [\App\Http\Controllers\Company\FileController::class, 'destroy'])
                ->name('files.destroy');
        });
        
        // ========================
        // SUPPORT MESSAGES
        // ========================
        
        // View messages (all company users)
        Route::get('/support', [\App\Http\Controllers\Company\SupportMessageController::class, 'index'])
            ->name('support');
        
        Route::get('/support/{message}', [\App\Http\Controllers\Company\SupportMessageController::class, 'show'])
            ->name('support.show');
        
        // Create messages (all company users)
        Route::post('/support', [\App\Http\Controllers\Company\SupportMessageController::class, 'store'])
            ->name('support.store');
        
        // Reply to messages (admin & support only)
        Route::middleware(['role:company_admin,support_user'])->group(function () {
            Route::post('/support/{message}/reply', [\App\Http\Controllers\Company\SupportMessageController::class, 'reply'])
                ->name('support.reply');
            
            Route::post('/support/{message}/close', [\App\Http\Controllers\Company\SupportMessageController::class, 'close'])
                ->name('support.close');
            
            Route::post('/support/{message}/reopen', [\App\Http\Controllers\Company\SupportMessageController::class, 'reopen'])
                ->name('support.reopen');
        });
        
        // ========================
        // AUDIT LOGS (Admin & Support Only)
        // ========================
        
        Route::middleware(['role:company_admin,support_user'])->group(function () {
            Route::get('/audit-logs', [\App\Http\Controllers\Company\AuditLogController::class, 'index'])
                ->name('audit-logs');
            
            Route::get('/audit-logs/{auditLog}', [\App\Http\Controllers\Company\AuditLogController::class, 'show'])
                ->name('audit-logs.show');
        });
    });
});

// ========================
// API ROUTES
// ========================

Route::prefix('api')->group(function () {
    require __DIR__.'/api.php';
});

// ========================
// CATCH-ALL FOR UNDEFINED ROUTES
// ========================

Route::fallback(function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }
        
        return redirect()->route('company.dashboard');
    }
    
    return redirect()->route('login');
});