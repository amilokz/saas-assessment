<?php

namespace App\Http\Controllers\Api;

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

        return response()->json([
            'data' => [
                'users' => $users,
                'roles' => $roles,
            ],
        ]);
    }

    public function invite(Request $request)
    {
        $company = Auth::user()->company;

        // Check permissions
        if (!Auth::user()->canInviteUsers()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check trial limitations
        if ($company->isOnTrial()) {
            $trialLimitations = (new \App\Services\TrialService())->getTrialLimitations();
            $currentUsers = $company->users()->count();
            
            if ($currentUsers >= $trialLimitations['max_users']) {
                return response()->json([
                    'error' => 'Trial accounts can only have ' . $trialLimitations['max_users'] . ' user(s).'
                ], 403);
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
            return response()->json([
                'error' => 'This user has already been invited.'
            ], 400);
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

        // Log the invitation
        AuditLogService::logUserInvitation($invitation);

        return response()->json([
            'message' => 'Invitation sent successfully',
            'data' => $invitation,
        ], 201);
    }

    public function updateRole(User $user, Request $request)
    {
        // Verify ownership and permissions
        if ($user->company_id !== Auth::user()->company_id || 
            !Auth::user()->isCompanyAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Cannot change own role
        if ($user->id === Auth::id()) {
            return response()->json([
                'error' => 'You cannot change your own role.'
            ], 403);
        }

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update([
            'role_id' => $validated['role_id'],
        ]);

        return response()->json([
            'message' => 'User role updated successfully',
            'data' => $user,
        ]);
    }

    public function destroy(User $user)
    {
        // Verify ownership and permissions
        if ($user->company_id !== Auth::user()->company_id || 
            !Auth::user()->isCompanyAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Cannot remove yourself
        if ($user->id === Auth::id()) {
            return response()->json([
                'error' => 'You cannot remove yourself from the team.'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User removed successfully',
        ]);
    }
}