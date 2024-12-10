<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    
    public function register(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'], // 'password_confirmation' is required
        ]);
    
        // Create the user in the database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password
        ]);
    
        // Generate an access token for the user
        $token = $user->createToken('auth_token')->plainTextToken;
    
        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully.',
            'data' => [
                'user' => $user,
                'access_token' => $token,
            ],
        ], 201); // Use 201 for successful creation
    }
    

    public function login(Request $request)
{
    // Input validation remains the same
    $request->validate([
        'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
        'password' => ['required', 'string', 'min:8'],
    ]);

    // Consider using Auth::attempt() and Auth::user() instead of manual user retrieval
    if (Auth::attempt($credentials = [
        'email' => $request->email,
        'password' => $request->password
    ])) {
        $user = Auth::user(); // This is cleaner than manual query

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return response
        return response()->json([
            'access_token' => $token,
            'user' => $user,
        ], 200);
    }

    // Authentication failed
    return response()->json([
        'message' => 'Invalid credentials',
    ], 401);
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
