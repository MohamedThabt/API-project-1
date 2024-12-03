<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function register(Request $request) {
        // When using Validator::make(), the result is a Validator instance, not an array
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Handle failed validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            // Use $validator->validated() to get the validated data
            $validatedData = $validator->validated();

            // create a new user
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // generate token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            // return the token as response
            return response()->json([
                'access_token' => $token,
                'user' => $user
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]); 

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $credentials = [
                'email' => $request->email, 
                'password' => $request->password
            ];

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'user' => $user
            ], 200);
          
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $exception->getMessage()
            ], 500);
        }     
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout successful'
        ], 200);
    }
}