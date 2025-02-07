<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\PersonalAccessTokenService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => ['required', 'string'], // Can be username, employee_id, or email
            'password' => ['required', 'string'],
        ]);

        $validatedData = $validator->validated();

        $login = $validatedData['login'];
        $password = $validatedData['password'];

        $user = User::where('username', $login)
            ->orWhere('employee_id', $login)
            ->orWhere('email', $login)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            // Create a new personal access token and assign some abilities to it.
            $token = $this->personalAccessTokenService->store($request, $user, ['create', 'read', 'update', 'delete']);
            return self::withOk(
                'User ' . self::MESSAGES['login'],
                [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            );
        }

        return self::withUnauthorized(self::MESSAGES['invalid_credentials']);
    }

    public function logout(Request $request, $tokenId = null)
    {
        if ($this->personalAccessTokenService->delete($request, $tokenId)) {
            return self::withOk(self::MESSAGES['logout']);
        }
        return self::withBadRequest(self::MESSAGES['system_error']);
    }
}
