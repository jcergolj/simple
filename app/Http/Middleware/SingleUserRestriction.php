<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SingleUserRestriction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If a user already exists, prevent registration
        if (User::exists()) {
            if ($request->expectsJson()) {
                return new \Illuminate\Http\JsonResponse([
                    'message' => 'Registration is disabled. Only one user is allowed per application.',
                ], 403);
            }

            // For web requests, redirect to login with message
            return to_route('login')->with('error', 'Registration is disabled. Only one user is allowed per application.');
        }

        return $next($request);
    }
}
