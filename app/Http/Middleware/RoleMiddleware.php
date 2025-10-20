<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::guard('employee')->check() ? Auth::guard('employee')->user() : Auth::guard('web')->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Get user roles
        if (Auth::guard('employee')->check()) {
            $userRoles = $user->role ? [$user->role->slug] : ['employee'];
        } else {
            // Get ALL user roles, not just the first one
            $userRoles = $user->roles()->pluck('slug')->toArray();
        }

        // Check if user has ANY of the required roles
        if (empty(array_intersect($userRoles, $roles))) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}