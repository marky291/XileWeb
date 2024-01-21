<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RagnarokAdministratorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the authenticated user
        $user = $request->user();

        // Check if the user is authenticated and has the specified email
        if ($user && $user->email === 'marky360@live.ie') {
            // If the user has the correct email, allow the request to proceed
            return $next($request);
        }

        // If the user is not authenticated or has the incorrect email, return unauthorized response
        return response('Unauthorized. You do not have permission to access this resource.', 403);
    }
}
