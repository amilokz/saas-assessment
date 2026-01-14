<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRegistrationRequest;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\TrialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyRegistrationController extends Controller
{
    protected $trialService;

    public function __construct(TrialService $trialService)
    {
        $this->trialService = $trialService;
    }

    public function create()
    {
        return view('company-registration');
    }

    public function store(CompanyRegistrationRequest $request)
    {
        // Create company
        $company = Company::create([
            'name' => $request->company_name,
            'admin_name' => $request->admin_name,
            'email' => $request->email,
            'business_type' => $request->business_type,
            'status' => 'pending',
        ]);

        // Get company admin role
        $companyAdminRole = Role::where('name', 'company_admin')->firstOrFail();

        // Create admin user
        $adminUser = User::create([
            'name' => $request->admin_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $company->id,
            'role_id' => $companyAdminRole->id,
            'email_verified_at' => now(),
        ]);

        // Start trial
        $this->trialService->startTrial($company);

        // Log registration
        AuditLogService::logCompanyRegistration($company);

        // Auto-login the admin user
        auth()->login($adminUser);

        return redirect()->route('company.dashboard')
            ->with('success', 'Company registered successfully! Your account is now in trial mode.');
    }
}