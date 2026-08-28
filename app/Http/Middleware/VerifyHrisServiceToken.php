<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyHrisServiceToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (empty($token) ||!hash_equals(config('services.token.public'),$token)) 
        {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
