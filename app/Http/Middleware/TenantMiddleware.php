<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Set company scope for all queries
        if ($user->company_id) {
            config(['company_id' => $user->company_id]);
        }

        return $next($request);
    }
}