<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMaintenanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Redirects to home if auth is disabled (e.g., during server migration).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('xilero.auth.enabled')) {
            return redirect()->route('home')->with('warning', config('xilero.auth.maintenance_message'));
        }

        return $next($request);
    }
}
