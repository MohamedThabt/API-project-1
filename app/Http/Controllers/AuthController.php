<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    
    
    public function register(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'], // 'password_confirmation' is required
            ]);
    
            // Create the user in the database
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']), // Hash the password
            ]);
    
            // Generate an access token for the user
            $token = $user->createToken('auth_token')->plainTextToken;
    
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully.',
                'data' => [
                    'user' => new UserResource($user), // Use a resource for structured user data
                    'access_token' => $token,
                ],
            ], 201); // Use 201 for successful creation
    
        } catch (ValidationException $e) {
            // Return validation errors in JSON format
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors(),
            ], 422); // 422 Unprocessable Entity
        }
    }
    
    public function login(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
                'password' => ['required', 'string', 'min:8'],
            ]);
    
            // Attempt authentication
            if (!Auth::attempt([
                'email' => $validatedData['email'],
                'password' => $validatedData['password'],
            ])) {
                // Return response if authentication fails
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials',
                ], 401); // 401 Unauthorized
            }
    
            // Retrieve the authenticated user
            $user = Auth::user();
    
            // Generate a token
            $token = $user->createToken('auth_token')->plainTextToken;
            
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful.',
                'data' => [
                    'user' => new UserResource($user), // Use a resource for structured user data
                    'access_token' => $token,
                ],
            ], 200); // 200 OK
    
        } catch (ValidationException $e) {
            // Return validation errors in JSON format
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors occurred.',
                'errors' => $e->errors(),
            ], 422); // 422 Unprocessable Entity
        }
    }

    public function logout(Request $request) {
        // Revoke the current access token
        $request->user()->currentAccessToken()->delete();

        // Return a success message
        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }
}
