<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Closure;

class Handler{
    
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json(['message' => 'Token expired'], 401);
    }
}
?>