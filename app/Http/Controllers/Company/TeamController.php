<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Check if user is company admin
        if (!$user->isCompanyAdmin()) {
            abort(403, 'Only company administrators can manage team.');
        }
        
        $company = $user->company;
        
        if (!$company) {
            abort(403, 'No company associated with your account.');
        }

        $users = User::where('company_id', $user->company_id)
                    ->with('role')
                    ->orderBy('created_at', 'desc')
                    ->get();

        $roles = Role::whereIn('name', ['company_admin', 'support_user', 'normal_user'])
            ->orderBy('id')
            ->get();

        $invitations = Invitation::where('company_id', $user->company_id)
            ->where('status', 'pending')
            ->with(['role', 'inviter'])
            ->orderBy('created_at', 'desc')
            ->get();

        $canInvite = !$company->isOnTrial() || $users->count() < 1;

        return view('company.team', compact(
            'company', 
            'users', 
            'roles', 
            'invitations',
            'canInvite'
        ));
    }

    public function invite(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isCompanyAdmin()) {
            abort(403, 'Only company administrators can invite users.');
        }
        
        $company = $user->company;

        // Check trial limitations
        if ($company->isOnTrial()) {
            $userCount = User::where('company_id', $user->company_id)->count();
            if ($userCount >= 1) {
                return redirect()->back()
                    ->with('error', 'Trial companies can only have 1 user.');
            }
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Check if email already exists
        $existingUser = User::where('email', $validated['email'])
            ->where('company_id', $user->company_id)
            ->first();

        if ($existingUser) {
            return redirect()->back()
                ->with('error', 'User already exists in your company.');
        }

        // Check if already invited
        $existingInvitation = Invitation::where('email', $validated['email'])
            ->where('company_id', $user->company_id)
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation) {
            return redirect()->back()
                ->with('error', 'User has already been invited.');
        }

        $role = Role::findOrFail($validated['role_id']);
        
        if ($role->name === 'super_admin') {
            abort(403, 'Cannot invite super admin.');
        }

        // Create invitation
        $invitation = Invitation::create([
            'company_id' => $company->id,
            'invited_by' => $user->id,
            'role_id' => $role->id,
            'email' => $validated['email'],
            'token' => Str::random(60),
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()->back()
            ->with('success', 'Invitation sent successfully.');
    }

    public function resendInvitation(Request $request, Invitation $invitation)
    {
        $user = Auth::user();
        
        if (!$user->isCompanyAdmin()) {
            abort(403, 'Only company administrators can resend invitations.');
        }
        
        // Verify ownership
        if ($invitation->company_id !== $user->company_id) {
            abort(403, 'Unauthorized.');
        }

        if (!$invitation->isPending()) {
            return redirect()->back()
                ->with('error', 'Cannot resend a non-pending invitation.');
        }

        $invitation->update([
            'token' => Str::random(60),
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()->back()
            ->with('success', 'Invitation resent successfully.');
    }

    public function revokeInvitation(Request $request, Invitation $invitation)
    {
        $user = Auth::user();
        
        if (!$user->isCompanyAdmin()) {
            abort(403, 'Only company administrators can revoke invitations.');
        }
        
        // Verify ownership
        if ($invitation->company_id !== $user->company_id) {
            abort(403, 'Unauthorized.');
        }

        $invitation->update([
            'status' => 'revoked',
            'revoked_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Invitation revoked successfully.');
    }

    public function updateRole(Request $request, User $user)
    {
        $currentUser = Auth::user();
        
        if (!$currentUser->isCompanyAdmin()) {
            abort(403, 'Only company administrators can update roles.');
        }
        
        if ($user->company_id !== $currentUser->company_id) {
            abort(403, 'Unauthorized.');
        }

        if ($user->id === $currentUser->id) {
            return redirect()->back()
                ->with('error', 'You cannot change your own role.');
        }

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        
        if ($role->name === 'super_admin') {
            abort(403, 'Cannot assign super admin role.');
        }

        $user->update([
            'role_id' => $validated['role_id'],
        ]);

        return redirect()->back()
            ->with('success', 'User role updated successfully.');
    }

    public function removeUser(Request $request, User $user)
    {
        $currentUser = Auth::user();
        
        if (!$currentUser->isCompanyAdmin()) {
            abort(403, 'Only company administrators can remove users.');
        }
        
        if ($user->company_id !== $currentUser->company_id) {
            abort(403, 'Unauthorized.');
        }

        if ($user->id === $currentUser->id) {
            return redirect()->back()
                ->with('error', 'You cannot remove yourself from the team.');
        }

        $user->delete();

        return redirect()->back()
            ->with('success', 'User removed successfully.');
    }
}