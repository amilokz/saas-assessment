<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // If user is super admin, redirect to default profile
        if ($user->isSuperAdmin()) {
            return redirect()->route('profile.edit');
        }
        
        // Get company for regular users
        $company = $user->company;
        
        if (!$company) {
            // If no company exists, show an error or redirect
            return view('company.no-company', [
                'user' => $user
            ]);
        }

        return view('company.profile', [
            'user' => $user,
            'company' => $company
        ]);
    }

    public function edit()
    {
        $user = Auth::user();
        
        // If user is super admin, redirect to default profile
        if ($user->isSuperAdmin()) {
            return redirect()->route('profile.edit');
        }
        
        $company = $user->company;
        
        if (!$company) {
            return view('company.no-company', [
                'user' => $user
            ]);
        }

        return view('company.profile-edit', [
            'user' => $user,
            'company' => $company
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            return redirect()->route('profile.edit');
        }
        
        $company = $user->company;
        
        if (!$company) {
            return redirect()->route('company.profile')->with('error', 'No company found.');
        }

        // Validate request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        // Update company information
        $company->update($validated);

        return redirect()->route('company.profile')->with('success', 'Company profile updated successfully.');
    }
}