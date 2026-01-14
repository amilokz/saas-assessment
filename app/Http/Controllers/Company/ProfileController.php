<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    /**
     * Display the company user's profile page.
     */
    public function index()
    {
        $user = Auth::user()->load('company', 'role');
        return view('company.profile', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['nullable', 'required_with:password'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        // Verify current password if changing password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'The provided password does not match your current password.'
                ]);
            }
            
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Track changes for audit log
        $changes = [];
        if ($user->name !== $validated['name']) {
            $changes['name'] = ['old' => $user->name, 'new' => $validated['name']];
        }
        if ($user->email !== $validated['email']) {
            $changes['email'] = ['old' => $user->email, 'new' => $validated['email']];
        }

        $user->update($validated);

        // Log profile update if AuditLogService exists
        if (!empty($changes) && class_exists(AuditLogService::class)) {
            try {
                AuditLogService::logProfileUpdate($user, $changes);
            } catch (\Exception $e) {
                // Log error but don't break the update
                \Log::error('Failed to log profile update: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Profile updated successfully.');
    }
}