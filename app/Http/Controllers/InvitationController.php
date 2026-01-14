<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InvitationController extends Controller
{
    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        return view('invitations.accept', compact('invitation'));
    }

    public function process(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if email already exists as user
        $existingUser = User::where('email', $invitation->email)->first();
        
        if ($existingUser) {
            return redirect()->route('login')
                ->with('error', 'An account with this email already exists. Please login instead.');
        }

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $invitation->email,
            'password' => Hash::make($validated['password']),
            'company_id' => $invitation->company_id,
            'role_id' => $invitation->role_id,
            'email_verified_at' => now(),
        ]);

        // Update invitation
        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Log in the user
        Auth::login($user);

        return redirect()->route('company.dashboard')
            ->with('success', 'Account created successfully. Welcome to ' . $invitation->company->name . '!');
    }

    public function decline($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $invitation->update([
            'status' => 'expired',
        ]);

        return view('invitations.declined');
    }
}