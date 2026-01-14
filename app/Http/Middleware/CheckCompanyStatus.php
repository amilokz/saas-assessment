<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        
        if (!$user->company) {
            return $next($request);
        }

        $company = $user->company;

        // Check if company is rejected
        if ($company->status === 'rejected') {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your company account has been rejected.');
        }

        // Check if company is suspended
        if ($company->status === 'suspended') {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your company account has been suspended.');
        }

        return $next($request);
    }
}