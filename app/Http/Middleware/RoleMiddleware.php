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
        
        if (!$user->role) {
            abort(403, 'User role not found.');
        }

        if (!in_array($user->role->name, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}