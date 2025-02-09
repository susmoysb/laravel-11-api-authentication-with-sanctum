<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Check if the authenticated user's token has the specified permission.
     *
     * @param \Illuminate\Http\Request $request The current request instance.
     * @param string $permission The required permission to check against the user's token.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException If the user's token does not have the required permission.
     */
    private function tokenCheck(Request $request, string $permission): bool
    {
        if (!$request->user()->tokenCan($permission)) {
            throw new HttpResponseException(self::withForbidden(self::MESSAGES['no_permission']));
        }
        return true;
    }

    /**
     * Display a listing of the users.
     *
     * This method retrieves all users if the authenticated user has the 'read' token permission.
     * If the user does not have the required permission, a forbidden response is returned.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     *
     * @return \Illuminate\Http\JsonResponse The response containing the list of users or a forbidden message.
     */
    public function index(Request $request): JsonResponse
    {
        self::tokenCheck($request, 'read');

        $users = User::all();
        return self::withOk('Users' . self::MESSAGES['retrieve'], $users);
    }

    /**
     * Store a newly created user in storage.
     *
     * This method handles the creation of a new user and stores it in the database.
     * It validates the incoming request data and returns a JSON response.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming request containing user data.
     *
     * @return \Illuminate\Http\JsonResponse  A JSON response indicating the result of the operation.
     *
     * @throws \Illuminate\Validation\ValidationException  If the request data fails validation.
     * @throws \Exception  If an error occurs during the user creation process.
     */
    public function store(Request $request)
    {
        self::tokenCheck($request, 'create');

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'username' => ['required', 'string', 'min:2', 'max:30', Rule::unique('users')],
            'employee_id' => ['required', 'string', 'min:2', 'max:30', Rule::unique('users')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $validatedData = $validator->validated();

        try {
            $user = User::create($validatedData);
            return self::withCreated('User ' . self::MESSAGES['store'], $user);
        } catch (Exception $e) {
            return self::withBadRequest(self::MESSAGES['system_error'], $e->getMessage() . ' ' . get_class($e));
        }
    }

    /**
     * Display the specified user.
     *
     * @param \Illuminate\Http\Request $request The incoming request instance.
     * @param string $id The ID of the user to retrieve.
     *
     * @return \Illuminate\Http\JsonResponse The response containing the user data or an error message.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        self::tokenCheck($request, 'read');

        // Use Query Builder
        $user = DB::table('users')->where('id', $id)->first();
        return $user
            ? self::withOk('User ' . self::MESSAGES['retrieve'], $user)
            : self::withNotFound('User ' . self::MESSAGES['not_found']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        self::tokenCheck($request, 'update');

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'username' => ['required', 'string', 'min:2', 'max:30', Rule::unique('users')->ignore($id)],
            'employee_id' => ['required', 'string', 'min:2', 'max:30', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
        ]);

        $validatedData = $validator->validated();

        // Use Eloquent Model Relationship
        $user = User::find($id);
        if ($user) {
            try {
                $user->update($validatedData);
                return self::withOk('User ' . self::MESSAGES['update'], $user);
            } catch (Exception $e) {
                return self::withBadRequest(self::MESSAGES['system_error'], $e->getMessage() . ' ' . get_class($e));
            }
        }
        return self::withNotFound('User ' . self::MESSAGES['not_found']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
