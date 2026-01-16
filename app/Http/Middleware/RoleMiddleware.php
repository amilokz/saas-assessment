<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // ✅ IMPORTANT: Check company access for non-super-admins
        if (!$user->isSuperAdmin()) {
            // If accessing company-specific route, verify ownership
            if ($request->route('company')) {
                $routeCompanyId = $request->route('company');
                if (is_object($routeCompanyId)) {
                    $routeCompanyId = $routeCompanyId->id;
                }
                
                if ($user->company_id != $routeCompanyId) {
                    abort(403, 'You do not have access to this company.');
                }
            }
        }
        
        // ✅ Check if user has a role
        if (!$user->role) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has no assigned role.');
        }

        // ✅ Check if user's role is in the allowed roles
        if (!in_array($user->role->name, $roles)) {
            abort(403, 'You do not have the required role to access this page.');
        }

        return $next($request);
    }
}