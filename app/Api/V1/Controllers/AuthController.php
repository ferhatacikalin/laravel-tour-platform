<?php

namespace App\Api\V1\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @group Authentication
 *
 * APIs for managing authentication
 */
class AuthController extends BaseController
{
    use ApiResponse;
    protected string $guard = 'api';

    /**
     * Login
     * 
     * Authenticate a user and return an access token.
     * 
     * @bodyParam email string required The email of the user. Example: user@example.com
     * @bodyParam password string required The password of the user. Minimum length: 5 characters. Example: password123
     * 
     * @response {
     *  "status": "success",
     *  "message": "Obtained token successfully",
     *  "data": {
     *    "user": {
     *      "id": 1,
     *      "name": "John Doe",
     *      "email": "user@example.com",
     *      "role": "tour_operator",
     *      "created_at": "2024-01-24T19:26:20.000000Z",
     *      "updated_at": "2024-01-24T19:26:20.000000Z"
     *    },
     *    "token": "1|laravel_sanctum_token"
     *  }
     * }
     * 
     * @response 401 {
     *  "status": "error",
     *  "message": "Wrong account or password",
     *  "data": {},
     *  "code": "200006"
     * }
     * 
     * @response 422 {
     *  "status": "error",
     *  "message": "Please enter your email",
     *  "data": {},
     *  "code": "200027"
     * }
     */
    public function login(Request $request): JsonResponse
    {
        $payload = $request->only('email', 'password');
        $rule = [
            'email'     => ['required','email'],
            'password'  => ['required','min:5']
        ];
        $message = [
            'email.required'    => 'COMMON_AUTH_EMAIL_EMPTY',
            'email.email'       => 'COMMON_AUTH_EMAIL_ERROR',
            'password.required' => 'COMMON_AUTH_PASSWORD_EMPTY',
            'password.min'      => 'COMMON_AUTH_PASSWORD_ERROR'
        ];
        $validator = Validator::make($payload, $rule, $message);
        if ($validator->fails()) {
            return $this->fail($validator->errors()->first(), [], 422);
        }
        $user = User::where('email', $payload['email'])->first();
        if (!$user || !Hash::check($payload['password'], $user->password)) {
            return $this->fail('COMMON_LOGIN_ERROR', [], 401);
        }
        $token = $user->createToken('Personal Access Token')->plainTextToken;
        return $this->success('COMMON_TOKEN_GET_SUCCESS', [
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Register
     * 
     * Register a new user and return an access token.
     * 
     * @bodyParam name string required The name of the user. Example: John Doe
     * @bodyParam email string required The email of the user. Example: user@example.com
     * @bodyParam password string required The password of the user. Minimum length: 8 characters. Example: password123
     * @bodyParam password_confirmation string required The password confirmation. Must match password. Example: password123
     * 
     * @response 201 {
     *  "status": "success",
     *  "message": "Obtained token successfully",
     *  "data": {
     *    "user": {
     *      "id": 1,
     *      "name": "John Doe",
     *      "email": "user@example.com",
     *      "role": "tour_operator",
     *      "created_at": "2024-01-24T19:26:20.000000Z",
     *      "updated_at": "2024-01-24T19:26:20.000000Z"
     *    },
     *    "token": "1|laravel_sanctum_token"
     *  }
     * }
     * 
     * @response 422 {
     *  "status": "error",
     *  "message": "Email already exists.",
     *  "data": {},
     *  "code": "200031"
     * }
     */
    public function register(Request $request): JsonResponse
    {
        $payload = $request->only('name', 'email', 'password', 'password_confirmation');
        $rule = [
            'name'      => ['required'],
            'email'     => ['required','email','unique:users'],
            'password'  => ['required','min:8','confirmed'],
            'password_confirmation' => ['required']
        ];
        $message = [
            'name.required'     => 'COMMON_AUTH_NAME_EMPTY',
            'email.required'    => 'COMMON_AUTH_EMAIL_EMPTY',
            'email.email'       => 'COMMON_AUTH_EMAIL_ERROR',
            'email.unique'      => 'COMMON_AUTH_EMAIL_EXISTS',
            'password.required' => 'COMMON_AUTH_PASSWORD_EMPTY',
            'password.min'      => 'COMMON_AUTH_PASSWORD_ERROR',
            'password.confirmed' => 'COMMON_AUTH_PASSWORD_CONFIRMATION_ERROR',
            'password_confirmation.required' => 'COMMON_AUTH_PASSWORD_CONFIRMATION_EMPTY'
        ];
        $validator = Validator::make($payload, $rule, $message);
        if ($validator->fails()) {
            return $this->fail($validator->errors()->first(), [], 422);
        }
        DB::beginTransaction();
        try {
            $user = User::create([
                'name'      => $payload['name'],
                'email'     => $payload['email'],
                'password'  => Hash::make($payload['password']),
                'role'      => 'tour_operator'
            ]);
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            DB::commit();
            return $this->success('COMMON_TOKEN_GET_SUCCESS', [
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->fail('COMMON_EMAIL_NOT_REGISTER', [], 422);
        }
    }

    /**
     * Get User Profile
     * 
     * Get the authenticated user's profile information.
     * 
     * @authenticated
     * 
     * @response {
     *  "status": "success",
     *  "message": "OK",
     *  "data": {
     *    "id": 1,
     *    "name": "John Doe",
     *    "email": "user@example.com",
     *    "role": "tour_operator",
     *    "created_at": "2024-01-24T19:26:20.000000Z",
     *    "updated_at": "2024-01-24T19:26:20.000000Z"
     *  }
     * }
     * 
     * @response 401 {
     *  "status": "error",
     *  "message": "UnAuthorized",
     *  "data": {},
     *  "code": "401"
     * }
     */
    public function me(): JsonResponse
    {
        return $this->success('COMMON_HTTP_OK', Auth::user());
    }
    protected function respondWithToken(string $token): JsonResponse
    {
        return $this->success('COMMON_TOKEN_GET_SUCCESS', [
            'access_token'  => $token,
            'token_type'    => 'Bearer',
            'expires_in'    => time() + (config('sanctum.expiration') * 60)
        ]);
    }

}
