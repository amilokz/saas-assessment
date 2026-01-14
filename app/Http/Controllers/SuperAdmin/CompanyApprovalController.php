<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\AuditLogService;
use App\Services\TrialService;
use Illuminate\Http\Request;

class CompanyApprovalController extends Controller
{
    public function index()
    {
        $companies = Company::withCount('users')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('super-admin.companies', compact('companies'));
    }

    public function approve(Company $company)
    {
        if (!in_array($company->status, ['pending', 'trial_pending_approval'])) {
            return redirect()->back()
                ->with('error', 'Company cannot be approved in its current status.');
        }

        $company->update([
            'status' => 'approved',
            'approved_at' => now(),
            'trial_ends_at' => null, // End trial when approved
        ]);

        // Log the approval
        AuditLogService::logCompanyApproval($company, true);

        return redirect()->route('super-admin.companies.index')
            ->with('success', 'Company approved successfully.');
    }

    public function reject(Company $company)
    {
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
    }

    public function suspend(Company $company)
    {
        $company->update([
            'status' => 'suspended',
        ]);

        return redirect()->back()
            ->with('success', 'Company suspended successfully.');
    }

    public function activate(Company $company)
    {
        $company->update([
            'status' => 'approved',
        ]);

        return redirect()->back()
            ->with('success', 'Company activated successfully.');
    }
}