<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{


    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (UnauthorizedHttpException $e) {
            return response()->json(['message' => 'Token expired'], 401);
        } catch (AuthenticationException $e) {
            return response()->json(['message' => 'Token expired or invalid'], 401);
        }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Please login to access this page.');
        }
        return $request->expectsJson() ? null : route('login');
    }
}
