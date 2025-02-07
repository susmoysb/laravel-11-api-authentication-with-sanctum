<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class PersonalAccessTokenService
{
    public function store(Request $request, User $user, $abilities)
    {
        $tokenResult = $user->createToken('auth_token', $abilities);
        $token = $tokenResult->plainTextToken;

        DB::table('personal_access_tokens')
            ->where('id', $tokenResult->accessToken->id)
            ->update(['ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);

        return $token;
    }

    public function delete(Request $request, $tokenId = null)
    {
        if (!$tokenId) {
            // Logout current session
            return $request->user()->currentAccessToken()->delete();
        }

        // Find token by ID
        $token = PersonalAccessToken::find($tokenId);
        if ($token && $token->tokenable_id === $request->user()->id) {
            return $token->delete();
        }
        
        return false;
    }
}
