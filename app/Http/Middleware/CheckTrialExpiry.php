<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTrialExpiry
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        
        if (!$user->company || $user->isSuperAdmin()) {
            return $next($request);
        }

        $company = $user->company;

        // Check if trial has expired
        if ($company->status === 'trial_pending_approval' && 
            $company->trial_ends_at && 
            $company->trial_ends_at->isPast()) {
            
            // Update company status if trial expired
            if ($company->status === 'trial_pending_approval') {
                $company->update(['status' => 'suspended']);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Your trial period has expired. Please contact support.'
                ], 403);
            }

            return redirect()->route('company.dashboard')
                ->with('error', 'Your trial period has expired. Please contact support.');
        }

        return $next($request);
    }
}