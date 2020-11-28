<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @package App\Http\Controllers
 */
class PassportView extends Controller
{
    /**
     * login to the API and create an access token
     *
     * @return Http\JsonResponse
     */
    public function login()
    {
        if (
            Auth::attempt(
                [
                    'email' => request('email'),
                    'password' => request('password')
                ]
            ) === true
        ) {
            $user = Auth::user();

            if ($user !== null) {

                $token = $user->createToken('costs-to-expect-api');

                return response()->json(
                    [
                        'id' => $this->hash->user()->encode(Auth::id()),
                        'type' => 'Bearer',
                        'token' => $token->accessToken,
                        'created' => $token->token->created_at,
                        'updated' => $token->token->updated_at,
                        'expires' => $token->token->expires_at
                    ],
                    201
                );
            }

            return response()->json(['message' => 'Unauthorised, credentials invalid'], 401);
        }

        return response()->json(['message' => 'Unauthorised, credentials invalid'], 401);
    }

    public function check()
    {
        return response()->json(['auth' => Auth::guard('api')->check()], 200);
    }

    /**
     * Register with the API will return the token
     *
     * @return Http\JsonResponse
     */
    public function register()
    {
        $validator = Validator::make(
            request()->all(),
            [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'password_confirmation' => 'required|same:password',
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'Validation error, please review the below',
                    'fields' => $validator->errors()
                ],
                422
            );
        }

        $input = request()->all();

        $input['password'] = Hash::make($input['password']);

        try {
            User::create($input);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to create user'], 500);
        }

        return response()->json([], 204);
    }

    /**
     * Return user details
     *
     * @return Http\JsonResponse
     */
    public function user()
    {
        $user = Auth::user();

        if ($user === null) {
            abort(403);
        }

        $user = $user->toArray();
        $user['id'] = $this->hash->user()->encode($user['id']);

        return response()->json($user, 200);
    }
}