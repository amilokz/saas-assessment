<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin bypass
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $company = $user->company;
        
        if (!$company) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'No company associated with your account.');
        }

        // Check company status
        switch ($company->status) {
            case 'suspended':
                auth()->logout();
                return redirect()->route('login')->with('error', 'Your company account has been suspended.');
                
            case 'pending':
                return redirect()->route('company.pending')->with('info', 'Your account is pending approval.');
                
            case 'trial_pending_approval':
                // Allow access to trial pages
                return $next($request);
                
            case 'active':
            case 'approved':
                return $next($request);
                
            default:
                auth()->logout();
                return redirect()->route('login')->with('error', 'Your company account has an invalid status.');
        }
    }
}