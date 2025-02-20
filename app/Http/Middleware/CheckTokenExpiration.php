<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }
        try{
            $payload = JWT::decode($token, new Key(env('JWT_SECRET', 'aN16QXIhkIwgsEKepsY0EdFq9eB2PTjQHxDxfwTQ6wczhLxOVWsUVR1al2nU3P9V'), 'HS256'));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token', 'error' => $e->getMessage()], 401);
        }
        if (!isset($payload->exp)) {
            return response()->json(['message' => 'Token expiration not set'], 401);
        }
        $expiration = Carbon::createFromTimestamp($payload->exp);
        if (Carbon::now()->greaterThan($expiration)) {
            return response()->json(['message' => 'Token expired'], 401);
        }
        
        return $next($request);
    }
}
