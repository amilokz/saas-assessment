<?php

namespace App\Services;

use App\Models\Invitation;
use Illuminate\Support\Facades\Mail;
use App\Mail\TeamInvitationMail;

class InvitationService
{
    public function sendInvitation(Invitation $invitation)
    {
        try {
            // Send email
            Mail::to($invitation->email)
                ->send(new TeamInvitationMail($invitation));

            return [
                'success' => true,
                'message' => 'Invitation email sent successfully.',
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to send invitation email: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to send invitation email.',
            ];
        }
    }

    public function resendInvitation(Invitation $invitation)
    {
        // Check if invitation is still valid
        if ($invitation->isExpired() || $invitation->isRevoked()) {
            return [
                'success' => false,
                'message' => 'Cannot resend expired or revoked invitation.',
            ];
        }

        // Update expiry
        $invitation->update([
            'expires_at' => now()->addDays(7),
        ]);

        // Resend email
        return $this->sendInvitation($invitation);
    }

    public function acceptInvitation($token, $userData)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return [
                'success' => false,
                'message' => 'Invalid or expired invitation token.',
            ];
        }

        // Check if email already registered
        $existingUser = \App\Models\User::where('email', $invitation->email)->first();
        if ($existingUser) {
            return [
                'success' => false,
                'message' => 'A user with this email already exists.',
            ];
        }

        // Create user
        $user = \App\Models\User::create([
            'name' => $userData['name'],
            'email' => $invitation->email,
            'password' => bcrypt($userData['password']),
            'company_id' => $invitation->company_id,
            'role_id' => $invitation->role_id,
            'email_verified_at' => now(),
        ]);

        // Update invitation
        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Log the acceptance
        AuditLogService::log('invitation_accepted', $invitation, 'User accepted invitation');

        return [
            'success' => true,
            'message' => 'Account created successfully.',
            'user' => $user,
        ];
    }

    public function revokeInvitation(Invitation $invitation)
    {
        if ($invitation->isAccepted()) {
            return [
                'success' => false,
                'message' => 'Cannot revoke an already accepted invitation.',
            ];
        }

        $invitation->update([
            'status' => 'revoked',
        ]);

        return [
            'success' => true,
            'message' => 'Invitation revoked successfully.',
        ];
    }

    public function getPendingInvitations($companyId)
    {
        return Invitation::where('company_id', $companyId)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->with(['role', 'inviter'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function cleanupExpiredInvitations()
    {
        $expired = Invitation::where('status', 'pending')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        return $expired;
    }
}