<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        self::tokenCheck($request, 'read');

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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
