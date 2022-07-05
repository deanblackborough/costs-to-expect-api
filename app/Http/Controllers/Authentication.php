<?php

namespace App\Http\Controllers;

use App\HttpResponse\Response;
use App\Notifications\ForgotPassword;
use App\Notifications\Registered;
use App\HttpOptionResponse\Auth\Check;
use App\HttpOptionResponse\Auth\CreateNewPassword;
use App\HttpOptionResponse\Auth\CreatePassword;
use App\HttpOptionResponse\Auth\Login;
use App\HttpOptionResponse\Auth\Register;
use App\HttpOptionResponse\Auth\UpdatePassword;
use App\HttpOptionResponse\Auth\UpdateProfile;
use App\User;
use Exception;
use Illuminate\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Authentication extends \Illuminate\Routing\Controller
{
    protected \App\HttpRequest\Hash $hash;

    public function __construct()
    {
        $this->hash = new \App\HttpRequest\Hash();
    }

    public function check(): Http\JsonResponse
    {
        return response()->json(['auth' => Auth::guard('api')->check()]);
    }

    public function optionsCheck(): Http\JsonResponse
    {
        $response = new Check([]);

        return $response->create()->response();
    }

    public function createPassword(Request $request): Http\JsonResponse
    {
        $email = Str::replaceFirst(' ', '+', urldecode($request->query('email')));
        $token = $request->query('token');

        $token_validation = DB::table('password_creates')
            ->where('email', '=', $email)
            ->first();

        if ($token_validation === null || Hash::check($token, $token_validation->token) === false) {
            return response()->json(
                [
                    'message'=> trans('auth.email-or-token-invalid')
                ],
                401
            );
        }

        $validator = Validator::make(
            $request->only(['password', 'password_confirmation']),
            [
                'password' => [
                    'required',
                    'min:12'
                ],
                'password_confirmation' => [
                    'required',
                    'same:password',
                ]
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => trans('responses.validation'),
                    'fields' => $validator->errors()
                ],
                422
            );
        }

        try {
            $user = User::with([])
                ->where('email', '=', $email)
                ->first();

            if ($user !== null) {
                $user->password = Hash::make($request->input('password'));
                $user->save();

                DB::table('password_creates')
                    ->where('email', '=', $request->input(['email']))
                    ->delete();

                return response()->json([], 204);
            }

            return response()->json(['message' => trans('auth.unable-to-find-account')], 404);
        } catch (Exception $e) {
            return response()->json(['message' => trans('auth.unable-to-create-password')], 500);
        }
    }

    public function optionsCreatePassword(): Http\JsonResponse
    {
        $response = new CreatePassword([]);

        return $response->create()->response();
    }

    public function createNewPassword(Request $request): Http\JsonResponse
    {
        $email = Str::replaceFirst(' ', '+', urldecode($request->query('email')));
        $token = $request->query('token');

        $tokens = DB::table('password_resets')
            ->where('email', '=', $email)
            ->first();

        if ($tokens === null || Hash::check($token, $tokens->token) === false) {
            return response()->json(
                [
                    'message'=> trans('auth.email-or-token-invalid')
                ],
                404
            );
        }

        $validator = Validator::make(
            $request->only(['password', 'password_confirmation']),
            [
                'password' => [
                    'required',
                    'min:12'
                ],
                'password_confirmation' => [
                    'required',
                    'same:password',
                ]
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => trans('responses.validation'),
                    'fields' => $validator->errors()
                ],
                422
            );
        }

        try {
            $user = User::with([])
                ->where('email', '=', $email)
                ->first();

            if ($user !== null) {
                $user->password = Hash::make($request->input('password'));
                $user->save();

                DB::table('password_resets')
                    ->where('email', '=', $request->input(['email']))
                    ->delete();

                return response()->json([], 204);
            }

            return response()->json(['message' => trans('auth.unable-to-find-account')], 500);
        } catch (Exception $e) {
            return response()->json(['message' => trans('auth.unable-to-create-password')], 500);
        }
    }

    public function optionsCreateNewPassword(): Http\JsonResponse
    {
        $response = new CreateNewPassword([]);

        return $response->create()->response();
    }

    public function forgotPassword(Request $request): Http\JsonResponse
    {
        $validator = Validator::make(
            $request->only(['email']),
            [
                'email' => 'required|email',
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => trans('responses.validation'),
                    'fields' => $validator->errors()
                ],
                422
            );
        }

        $email = $request->input('email');

        $user = User::with([])
            ->where('email', '=', $email)
            ->first();

        if ($user !== null) {
            try {
                $create_token = Str::random(20);

                DB::table('password_resets')->updateOrInsert(
                    [
                        'email' => $email,
                    ],
                    [
                        'email' => $email,
                        'token' => Hash::make($create_token)
                    ]
                );

                if (app()->environment() === 'production' && $request->query('send') === null) {
                    $user->notify(new ForgotPassword($user, $create_token));
                }
            } catch (Exception $e) {
                return response()->json(['error' => trans('auth.unable-process-forgot-password')], 500);
            }

            return response()->json(
                [
                    'message' => trans('auth.success-forgot-password-request'),
                    'uris' => [
                        'create-new-password' => [
                            'uri' => Config::get('api.app.version.prefix') . '/auth/create-new-password?token=' . $create_token . '&email=' . $email,
                            'parameters' => [
                                'token' => $create_token,
                                'email' => $email
                            ]
                        ]
                    ]
                ],
                201
            );
        }

        return response()->json(['message' => trans('auth.unable-to-find-account')], 404);
    }

    public function optionsForgotPassword(): Http\JsonResponse
    {
        $response = new \App\HttpOptionResponse\Auth\ForgotPassword([]);

        return $response->create()->response();
    }

    public function login(Request $request): Http\JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => [
                    'required',
                    'string'
                ],
                'password' => [
                    'required',
                    'min:12'
                ],
                'device_name' => [
                    'sometimes',
                    'string'
                ]
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => trans('responses.validation'),
                    'fields' => $validator->errors()
                ],
                422
            );
        }

        if (
            Auth::attempt(
                [
                    'email' => request('email'),
                    'password' => request('password')
                ]
            ) === true
        ) {
            $user = Auth::guard('api')->user();

            if ($user !== null) {
                $request->user()->revokeOldTokens();

                $token_name = 'costs-to-expect-api';
                if ($request->input('device_name') !== null) {
                    $token_name = str::slug($request->input('device_name')) . ':' . $token_name;
                }

                $token = $request->user()->createToken($token_name);
                return response()->json(
                    [
                        'id' => $this->hash->user()->encode($user->id),
                        'type' => 'Bearer',
                        'token' => $token->plainTextToken,
                    ],
                    201
                );
            }

            return Response::authenticationFailed();
        }

        return Response::authenticationFailed();
    }

    public function optionsLogin(): Http\JsonResponse
    {
        $response = new Login([]);

        return $response->create()->response();
    }

    public function logout(): Http\JsonResponse
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => trans('auth.signed-out')], 200);
    }

    public function register(Request $request): Http\JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => [
                    'required',
                    'email',
                    Rule::unique(User::class, 'email')
                ]
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => trans('responses.validation'),
                    'fields' => $validator->errors()
                ],
                422
            );
        }

        try {
            $email = $request->input('email');

            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make(Str::random(20));
            $user->save();

            $create_token = Str::random(20);

            DB::table('password_creates')->updateOrInsert(
                [
                    'email' => $email,
                ],
                [
                    'email' => $email,
                    'token' => Hash::make($create_token)
                ]
            );

            if ($request->query('send') === null && app()->environment() === 'production') {
                $user->notify(new Registered($user, $create_token));
            }
        } catch (Exception $e) {
            return Response::unableToCreateAccount($e);
        }

        return response()->json(
            [
                'message' => trans('auth.success-account-created'),
                'uris' => [
                    'create-password' => [
                        'uri' => Config::get('api.app.version.prefix') . '/auth/create-password?token=' . $create_token . '&email=' . $email,
                        'parameters' => [
                            'token' => $create_token,
                            'email' => $email
                        ]
                    ]
                ]
            ],
            201
        );
    }

    public function optionsRegister(): Http\JsonResponse
    {
        $response = new Register([]);

        return $response->create()->response();
    }

    public function updatePassword(Request $request): Http\JsonResponse
    {
        $validator = Validator::make(
            $request->only(['password', 'password_confirmation']),
            [
                'password' => [
                    'required',
                    'min:12'
                ],
                'password_confirmation' => [
                    'required',
                    'same:password',
                ]
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => trans('responses.validation'),
                    'fields' => $validator->errors()
                ],
                422
            );
        }

        $user = auth()->guard('api')->user();

        if ($user !== null) {
            $user->password = Hash::make($request->input('password'));
            $user->save();

            return response()->json([], 204);
        }

        return Response::authenticationFailed();
    }

    public function optionsUpdateProfile(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        $response = new UpdateProfile(['view'=> $user !== null && $user->id !== null, 'manage'=> $user !== null && $user->id !== null]);

        return $response->create()->response();
    }

    public function optionsUpdatePassword(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        $response = new UpdatePassword(['view'=> $user !== null && $user->id !== null, 'manage'=> $user !== null && $user->id !== null]);

        return $response->create()->response();
    }

    public function updateProfile(Request $request): Http\JsonResponse
    {
        $validator = Validator::make(
            $request->only(['name', 'email']),
            [
                'name' => [
                    'sometimes'
                ],
                'email' => [
                    'sometimes',
                    'email'
                ]
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => trans('responses.validation'),
                    'fields' => $validator->errors()
                ],
                422
            );
        }

        $user = auth()->guard('api')->user();

        if ($user !== null) {
            $fields = [];
            if ($request->input('name') !== null) {
                $fields['name'] = $request->input('name');
            }
            if ($request->input('email') !== null) {
                $fields['email'] = $request->input('email');
            }

            if (count($fields) === 0) {
                return Response::nothingToPatch();
            }

            try {
                foreach ($fields as $field => $value) {
                    $user->$field = $value;
                }

                $user->save();
            } catch (Exception $e) {
                return response()->json(['message' => trans('auth.unable-to-update-profile')], 401);
            }

            return response()->json([], 204);
        }

        return Response::authenticationFailed();
    }

    public function user(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user !== null) {
            $tokens = [];
            foreach ($user->tokens as $token) {
                $tokens[] = [
                    'id' => $token->id,
                    'name' => $token->name,
                    'token' => $token->token,
                    'created' => $token->created_at,
                    'last_used_at' => $token->last_used_at
                ];
            }

            $user = [
                'id' => $this->hash->user()->encode($user->id),
                'name' => $user->name,
                'email' => $user->email,
                'tokens' => [
                    'uri' => route('auth.user.token.list', [], false),
                    'count' => count($tokens),
                    'collection' => $tokens
                ]
            ];

            return response()->json($user);
        }

        return Response::authenticationFailed();
    }

    public function tokens(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user !== null) {
            $tokens = [];
            foreach ($user->tokens as $token) {
                $tokens[] = [
                    'id' => $token->id,
                    'name' => $token->name,
                    'token' => $token->token,
                    'created' => $token->created_at,
                    'last_used_at' => $token->last_used_at
                ];
            }

            return response()->json($tokens);
        }

        return Response::authenticationFailed();
    }

    public function token($token_id): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user !== null) {
            $tokens = [];
            foreach ($user->tokens as $token) {
                $tokens[$token->id] = [
                    'id' => $token->id,
                    'name' => $token->name,
                    'token' => $token->token,
                    'created' => $token->created_at,
                    'last_used_at' => $token->last_used_at
                ];
            }

            if (array_key_exists($token_id, $tokens)) {
                return response()->json($tokens[$token_id]);
            }

            return Response::notFound();
        }

        return Response::authenticationFailed();
    }

    public function deleteToken($token_id): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        if ($user !== null) {
            $tokens = [];
            foreach ($user->tokens as $token) {
                $tokens[$token->id] = [
                    'id' => $token->id,
                    'name' => $token->name,
                    'token' => $token->token,
                    'created' => $token->created_at,
                    'last_used_at' => $token->last_used_at
                ];
            }

            if (array_key_exists($token_id, $tokens)) {
                $user->tokens()->where('id', $token_id)->delete();
                return Response::successNoContent();
            }

            return Response::notFound();
        }

        return Response::authenticationFailed();
    }

    public function optionsUser(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        $response = new \App\HttpOptionResponse\Auth\User(['view'=> $user !== null && $user->id !== null]);

        return $response->create()->response();
    }

    public function optionsTokens(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        $response = new \App\HttpOptionResponse\Auth\Tokens(['view'=> $user !== null && $user->id !== null]);

        return $response->create()->response();
    }

    public function optionsToken(): Http\JsonResponse
    {
        $user = auth()->guard('api')->user();

        $response = new \App\HttpOptionResponse\Auth\Token([
            'view'=> $user !== null && $user->id !== null,
            'manage'=> $user !== null && $user->id !== null,
        ]);

        return $response->create()->response();
    }
}
