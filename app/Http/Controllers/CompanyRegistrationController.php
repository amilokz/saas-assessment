<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRegistrationRequest;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyRegistrationController extends Controller
{
    public function create()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.company-registration');
    }

    public function store(CompanyRegistrationRequest $request)
    {
        try {
            \DB::beginTransaction();

            // Check if email already exists
            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This email is already registered.');
            }

            // Create company
            $company = Company::create([
                'name' => $request->company_name,
                'admin_name' => $request->admin_name,
                'email' => $request->email,
                'business_type' => $request->business_type,
                'status' => 'trial_pending_approval',
                'trial_ends_at' => now()->addDays(7),
            ]);

            // Get or create company admin role
            $companyAdminRole = Role::firstOrCreate(
                ['name' => 'company_admin'],
                [
                    'display_name' => 'Company Administrator',
                    'description' => 'Full access to company management',
                ]
            );

            // Create admin user
            $adminUser = User::create([
                'name' => $request->admin_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'company_id' => $company->id,
                'role_id' => $companyAdminRole->id,
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            \DB::commit();

            // Login the user
            auth()->login($adminUser);

            // Simple redirect without loading relationships
            return redirect()->route('company.trial-status');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            \Log::error('Company registration failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Registration failed. Please try again.');
        }
    }

    // Simple trial status without complex queries
    public function trialStatus()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Get company without loading relationships
        $company = Company::select(['id', 'name', 'status', 'trial_ends_at'])
                         ->where('id', $user->company_id)
                         ->first();
        
        if (!$company || $company->status !== 'trial_pending_approval') {
            return redirect()->route('company.dashboard');
        }

        // Calculate trial days
        $trialDaysLeft = 0;
        if ($company->trial_ends_at) {
            $trialDaysLeft = now()->diffInDays($company->trial_ends_at, false);
            $trialDaysLeft = max(0, $trialDaysLeft);
        }

        return view('company.trial-status', [
            'company' => $company,
            'trialDaysLeft' => $trialDaysLeft,
        ]);
    }
}