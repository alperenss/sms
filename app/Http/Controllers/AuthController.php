<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Create a new unique user and create auth token
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $name = $request->name;
            $email = $request->email;
            $password = $request->password;

            //Create user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password)
            ]);

            //Check user create
            if (!$user) {
                throw new \Exception('Unexpected error.', 400);
            }

            //Create token
            $token = $user->createToken('register_token')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

            //return success json response with http 201 status code
            return response()->json(['status' => true, 'values' => $response], 201);

        } catch (\Exception $e) {
            Log::error('AuthController/login: '.$e->getMessage());
            return response()->json(['status' => false, 'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()]], 400);
        }
    }

    /**
     * Login user and create a new auth token
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $email = $request->email;
            $password = $request->password;

            //Check user
            $user = User::where('email', $email)->first();

            if (!$user || !Hash::check($password, $user->password)) {
                return response()->json(['status' => false, 'error' => ['code' => 401, 'message' => 'user not found']], 401);
            }

            //Create new token
            $token = $user->createToken('login_token')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

            //return success json response with http 201 status code
            return response()->json(['status' => true, 'values' => $response], 201);

        } catch (\Exception $e) {
            Log::error('AuthController/login: '.$e->getMessage());
            return response()->json(['status' => false, 'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()]], 400);
        }
    }

    /**
     * Delete user auth tokens
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            //Delete user tokens
            auth()->user()->tokens()->delete();

            //return success json response with http 200 status code
            return response()->json(['status' => 'true', 'values' => ['message' => 'Logged out.']], 200);

        } catch (\Exception $e) {
            Log::error('AuthController/logout: '.$e->getMessage());
            return response()->json(['status' => false, 'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()]], 400);
        }

    }
}
