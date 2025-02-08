<?php

namespace App\Http\Middleware;

use App\Classes\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->is('api/login') || $request->is('api/register')) {
            return $next($request);
        }

        $bearerToken = trim($request->bearerToken());
        if (!$bearerToken) {
            return ApiResponse::withUnauthorized('Token is required.');
        }

        $token = PersonalAccessToken::findToken($bearerToken);
        if (!$token) {
            return ApiResponse::withUnauthorized('Invalid Token.');
        }

        return $next($request);
    }
}
