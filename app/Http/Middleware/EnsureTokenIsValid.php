<?php

namespace App\Http\Middleware;

use App\Classes\ApiResponse;
use App\Http\Controllers\BaseController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if (!$request->expectsJson()) {
            return ApiResponse::withNotAcceptable(BaseController::MESSAGES['accept_header_error']);
        }

        if ($request->is('api/login') || $request->is('api/register')) {
            return $next($request);
        }

        $bearerToken = trim($request->bearerToken());
        if (!$bearerToken) {
            return ApiResponse::withUnauthorized('Token is required.');
        }

        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return ApiResponse::withUnauthorized(BaseController::MESSAGES['unauthenticated']);
        }

        // $token = PersonalAccessToken::findToken($bearerToken);
        // if (!$token) {
        //     return ApiResponse::withUnauthorized('Invalid Token.');
        // }

        return $next($request);
    }
}
