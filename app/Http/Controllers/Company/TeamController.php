<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;
        
        $users = $company->users()
            ->with('role')
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = Role::whereIn('name', ['company_admin', 'support_user', 'normal_user'])
            ->get();

        // FIX: Changed variable name from $pendingInvitations to $invitations
        $invitations = $company->invitations()
            ->where('status', 'pending')
            ->with(['role', 'inviter'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('company.team', compact('company', 'users', 'roles', 'invitations'));
    }

    public function invite(Request $request)
    {
        $company = Auth::user()->company;

        // Check trial limitations
        if ($company->isOnTrial()) {
            $trialLimitations = (new \App\Services\TrialService())->getTrialLimitations();
            $currentUsers = $company->users()->count();
            
            if ($currentUsers >= $trialLimitations['max_users']) {
                return redirect()->back()
                    ->with('error', 'Trial accounts can only have ' . $trialLimitations['max_users'] . ' user(s).');
            }
        }

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Check if already invited
        $existingInvitation = $company->invitations()
            ->where('email', $validated['email'])
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation) {
            return redirect()->back()
                ->with('error', 'This user has already been invited.');
        }

        // Create invitation
        $invitation = Invitation::create([
            'company_id' => $company->id,
            'invited_by' => Auth::id(),
            'role_id' => $validated['role_id'],
            'email' => $validated['email'],
            'token' => Str::random(60),
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        // Send invitation email (in a real app)
        // Mail::to($validated['email'])->send(new TeamInvitationMail($invitation));

        // Log the invitation
        AuditLogService::logUserInvitation($invitation);

        return redirect()->back()
            ->with('success', 'Invitation sent successfully.');
    }

    public function resendInvitation(Invitation $invitation)
    {
        // Verify ownership
        if ($invitation->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        if ($invitation->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Cannot resend invitation. It may have expired or been accepted.');
        }

        // Update expiry and resend
        $invitation->update([
            'expires_at' => now()->addDays(7),
        ]);

        // Resend email (in a real app)
        // Mail::to($invitation->email)->send(new TeamInvitationMail($invitation));

        return redirect()->back()
            ->with('success', 'Invitation resent successfully.');
    }

    public function revokeInvitation(Invitation $invitation)
    {
        // Verify ownership
        if ($invitation->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $invitation->update([
            'status' => 'revoked',
        ]);

        return redirect()->back()
            ->with('success', 'Invitation revoked successfully.');
    }

    public function updateRole(User $user, Request $request)
    {
        // Verify ownership and permissions
        if ($user->company_id !== Auth::user()->company_id || 
            !Auth::user()->isCompanyAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Cannot change own role
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'You cannot change your own role.');
        }

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update([
            'role_id' => $validated['role_id'],
        ]);

        return redirect()->back()
            ->with('success', 'User role updated successfully.');
    }

    public function removeUser(User $user)
    {
        // Verify ownership and permissions
        if ($user->company_id !== Auth::user()->company_id || 
            !Auth::user()->isCompanyAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Cannot remove yourself
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'You cannot remove yourself from the team.');
        }

        $user->delete();

        return redirect()->back()
            ->with('success', 'User removed successfully.');
    }
}