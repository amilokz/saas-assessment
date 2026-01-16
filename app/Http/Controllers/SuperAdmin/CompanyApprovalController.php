<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\AuditLog;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompanyApprovalController extends Controller
{
    public function __construct()
    {
        // Middleware already applied in routes, but keeping for clarity
        $this->middleware('auth');
        $this->middleware('role:super_admin');
    }

    public function index()
    {
        $companies = Company::withCount('users')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('super-admin.companies', compact('companies'));
    }

    /**
     * Display the specified company details.
     */
    public function show(Company $company)
    {
        try {
            // Load company with all relationships
            $company->load([
                'users' => function ($query) {
                    $query->with('role')->latest();
                },
                'subscriptions' => function ($query) {
                    $query->with('plan')->latest();
                },
                'files' => function ($query) {
                    $query->latest()->limit(10);
                },
                'messages' => function ($query) {
                    $query->latest()->limit(10);
                }
            ]);

            // Get company audit logs
            $auditLogs = AuditLog::where('company_id', $company->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Calculate storage usage
            $totalStorage = $company->files->sum('size');
            $totalStorageMB = round($totalStorage / (1024 * 1024), 2);

            // Get current subscription
            $currentSubscription = $company->subscriptions
                ->where('status', 'active')
                ->first();

            return view('super-admin.companies.show', compact(
                'company',
                'auditLogs',
                'totalStorageMB',
                'currentSubscription'
            ));
            
        } catch (\Exception $e) {
            return redirect()->route('super-admin.companies.index')
                ->with('error', 'Failed to load company details: ' . $e->getMessage());
        }
    }

    public function approve(Company $company)
    {
        try {
            if (!in_array($company->status, ['pending', 'trial_pending_approval'])) {
                return redirect()->back()
                    ->with('error', 'Company cannot be approved in its current status.');
            }

            $company->update([
                'status' => 'approved',
                'approved_at' => now(),
                'trial_ends_at' => null,
            ]);

            // Log the approval
            AuditLogService::logCompanyApproval($company, true);

            return redirect()->route('super-admin.companies.index')
                ->with('success', 'Company approved successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve company: ' . $e->getMessage());
        }
    }

    public function reject(Company $company)
    {
        try {
            if (!in_array($company->status, ['pending', 'trial_pending_approval'])) {
                return redirect()->back()
                    ->with('error', 'Company cannot be rejected in its current status.');
            }

            $company->update([
                'status' => 'rejected',
                'rejected_at' => now(),
            ]);

            // Log the rejection
            AuditLogService::logCompanyApproval($company, false);

            return redirect()->route('super-admin.companies.index')
                ->with('success', 'Company rejected successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reject company: ' . $e->getMessage());
        }
    }

    public function suspend(Company $company)
    {
        try {
            if ($company->status === 'suspended') {
                return redirect()->back()
                    ->with('error', 'Company is already suspended.');
            }

            $company->update([
                'status' => 'suspended',
                'suspended_at' => now(),
            ]);

            // Log suspension
            AuditLogService::logCompanySuspension($company, true);

            return redirect()->back()
                ->with('success', 'Company suspended successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to suspend company: ' . $e->getMessage());
        }
    }

    public function activate(Company $company)
    {
        try {
            if ($company->status !== 'suspended') {
                return redirect()->back()
                    ->with('error', 'Only suspended companies can be activated.');
            }

            $company->update([
                'status' => 'approved',
                'suspended_at' => null,
            ]);

            // Log activation
            AuditLogService::logCompanySuspension($company, false);

            return redirect()->back()
                ->with('success', 'Company activated successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to activate company: ' . $e->getMessage());
        }
    }

    /**
     * Search companies
     */
    public function search(Request $request)
    {
        try {
            $request->validate([
                'search' => 'nullable|string|max:255',
                'status' => 'nullable|string|in:pending,approved,rejected,suspended,trial_pending_approval',
            ]);

            $search = $request->input('search');
            $status = $request->input('status');

            $companies = Company::withCount('users')
                ->when($search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('admin_name', 'like', "%{$search}%")
                          ->orWhere('business_type', 'like', "%{$search}%");
                    });
                })
                ->when($status, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString(); // Better than appends()

            return view('super-admin.companies', compact('companies'));
            
        } catch (\Exception $e) {
            return redirect()->route('super-admin.companies.index')
                ->with('error', 'Search failed: ' . $e->getMessage());
        }
    }

    /**
     * Bulk actions (Optional but useful)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,suspend,activate',
            'companies' => 'required|array',
            'companies.*' => 'exists:companies,id',
        ]);

        $action = $request->input('action');
        $companyIds = $request->input('companies');
        $successCount = 0;
        $failedCount = 0;

        foreach ($companyIds as $companyId) {
            try {
                $company = Company::findOrFail($companyId);
                
                switch ($action) {
                    case 'approve':
                        if (in_array($company->status, ['pending', 'trial_pending_approval'])) {
                            $company->update(['status' => 'approved', 'approved_at' => now()]);
                            AuditLogService::logCompanyApproval($company, true);
                            $successCount++;
                        } else {
                            $failedCount++;
                        }
                        break;
                        
                    case 'reject':
                        if (in_array($company->status, ['pending', 'trial_pending_approval'])) {
                            $company->update(['status' => 'rejected', 'rejected_at' => now()]);
                            AuditLogService::logCompanyApproval($company, false);
                            $successCount++;
                        } else {
                            $failedCount++;
                        }
                        break;
                        
                    case 'suspend':
                        if ($company->status !== 'suspended') {
                            $company->update(['status' => 'suspended', 'suspended_at' => now()]);
                            AuditLogService::logCompanySuspension($company, true);
                            $successCount++;
                        } else {
                            $failedCount++;
                        }
                        break;
                        
                    case 'activate':
                        if ($company->status === 'suspended') {
                            $company->update(['status' => 'approved', 'suspended_at' => null]);
                            AuditLogService::logCompanySuspension($company, false);
                            $successCount++;
                        } else {
                            $failedCount++;
                        }
                        break;
                }
            } catch (\Exception $e) {
                $failedCount++;
                \Log::error("Bulk action failed for company {$companyId}: " . $e->getMessage());
            }
        }

        $message = "Completed: {$successCount} successful, {$failedCount} failed.";
        return redirect()->route('super-admin.companies.index')
            ->with($successCount > 0 ? 'success' : 'warning', $message);
    }
}