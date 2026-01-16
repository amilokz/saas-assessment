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

        if ($company->status === 'trial_pending_approval' && 
            $company->trial_ends_at && 
            $company->trial_ends_at->isPast()) {
            
            $company->update(['status' => 'suspended']);
            
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->with('error', 'Your 7-day trial has expired. Please contact support.');
        }

        if ($company->isOnTrial()) {
            $this->applyTrialLimitations($request, $company);
        }

        return $next($request);
    }

    private function applyTrialLimitations(Request $request, $company)
    {
        $routeName = $request->route()->getName();
        
        if ($routeName === 'team.invite' && $company->users()->count() >= 1) {
            abort(403, 'Trial companies can only have 1 user.');
        }
        
        if ($routeName === 'files.store' && $company->files()->count() >= 2) {
            abort(403, 'Trial companies can only upload 2 files.');
        }
    }
}