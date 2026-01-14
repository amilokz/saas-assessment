<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        
        // Super admin can access all
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // For company users, check if they're accessing their own company data
        $companyId = $request->route('company') ?? 
                     $request->input('company_id') ?? 
                     $request->company_id;

        if ($companyId && $user->company_id != $companyId) {
            abort(403, 'Access denied to this company data.');
        }

        return $next($request);
    }
}