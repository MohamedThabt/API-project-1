<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Return validation errors with status code 400
            return response()->json($validator->errors(), 400);
        }

        try {
            // Retrieve validated data
            $validatedData = $validator->validated();

            // Create a new user in the database
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']), // Hash the password
            ]);

            // Generate an access token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return the token and user data as a response
            return response()->json([
                'access_token' => $token,
                'user' => $user,
            ], 200);
        } catch (\Exception $exception) {
            // Handle unexpected errors during user creation
            return response()->json([
                'message' => 'Registration failed',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle user login.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Return validation errors with status code 400
            return response()->json($validator->errors(), 400);
        }

        try {
            // Prepare credentials for authentication
            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            // Attempt to authenticate the user
            if (!Auth::attempt($credentials)) {
                // Return error if credentials are invalid
                return response()->json([
                    'message' => 'Invalid credentials',
                ], 401);
            }

            // Retrieve the authenticated user
            $user = User::where('email', $request->email)->first();

            // Generate an access token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return the token and user data as a response
            return response()->json([
                'access_token' => $token,
                'user' => $user,
            ], 200);
        } catch (\Exception $exception) {
            // Handle unexpected errors during login
            return response()->json([
                'message' => 'Login failed',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle user logout.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke the current access token
        $request->user()->currentAccessToken()->delete();

        // Return a success message
        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }
}
