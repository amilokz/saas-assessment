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
        
        // ✅ Super admin can access everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // ✅ For company-bound users: Ensure they can only access their company's data
        // Method 1: Check route parameters
        if ($request->route('company')) {
            $routeCompanyId = $request->route('company');
            if (is_object($routeCompanyId)) {
                $routeCompanyId = $routeCompanyId->id;
            }
            
            if ($user->company_id != $routeCompanyId) {
                abort(403, 'Access denied to this company\'s data.');
            }
        }

        // Method 2: Check for company_id in request
        if ($request->has('company_id') && $user->company_id != $request->input('company_id')) {
            abort(403, 'Access denied. Company ID mismatch.');
        }

        // Method 3: For resource IDs, we'll rely on model binding with Global Scopes
        // This ensures users can only access their own company's records
        
        return $next($request);
    }
}