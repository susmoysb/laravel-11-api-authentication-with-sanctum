<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
