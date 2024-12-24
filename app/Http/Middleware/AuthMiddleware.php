<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            Log::info('User not authenticated, redirecting to login.');
            return redirect(url('/login'))->with('error', 'Please login to access this page.');
        }

        Log::info('User authenticated: ', ['user_id' => Auth::id()]);
        return $next($request);
    }
}
