<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MultiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated as admin or employee
        if (Auth::guard('web')->check() || Auth::guard('employee')->check()) {
            return $next($request);
        }

        // If not authenticated, redirect to login
        if (! $request->expectsJson()) {
            return redirect()->route('login');
        }
    }
}
