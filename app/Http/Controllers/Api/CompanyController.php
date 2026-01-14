<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function show(Company $company)
    {
        // Verify ownership
        if ($company->id !== Auth::user()->company_id && !Auth::user()->isSuperAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $company->load('users', 'activeSubscription.plan');

        return response()->json([
            'data' => $company,
        ]);
    }

    public function update(Request $request, Company $company)
    {
        // Verify ownership
        if ($company->id !== Auth::user()->company_id || !Auth::user()->isCompanyAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'business_type' => 'sometimes|string|max:255',
        ]);

        $company->update($validated);

        return response()->json([
            'message' => 'Company updated successfully',
            'data' => $company,
        ]);
    }

    public function stats()
    {
        $company = Auth::user()->company;

        $stats = [
            'total_users' => $company->users()->count(),
            'total_files' => $company->files()->count(),
            'total_messages' => $company->messages()->count(),
            'active_subscription' => $company->hasActiveSubscription(),
            'trial_ends_at' => $company->trial_ends_at,
            'status' => $company->status,
        ];

        return response()->json([
            'data' => $stats,
        ]);
    }
}