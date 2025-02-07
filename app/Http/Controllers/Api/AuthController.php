<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\PersonalAccessTokenService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function __construct(private PersonalAccessTokenService $personalAccessTokenService) {}

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'username' => ['required', 'string', 'min:2', 'max:30', Rule::unique('users')],
            'employee_id' => ['required', 'string', 'min:2', 'max:30', Rule::unique('users')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $validatedData = $validator->validated();

        try {
            DB::beginTransaction();
            $user = User::create($validatedData);

            // Create a new personal access token and assign some abilities to it.
            $token = $this->personalAccessTokenService->store($request, $user, ['create', 'read', 'update', 'delete']);
            DB::commit();
            
            return self::withCreated(
                'User ' . self::MESSAGES['register'],
                [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            );
        } catch (Exception $e) {
            DB::rollBack();
            return self::withBadRequest(self::MESSAGES['system_error'], $e->getMessage() . ' ' . get_class($e));
        }
    }
}
