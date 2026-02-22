<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        try {
            // Validate incoming request data
            $validated = $request->validate([
                'name'                  => ['required', 'string', 'regex:/^[A-Za-z\s]+$/', 'min:3', 'max:255'],
                'email'                 => 'required|email|unique:users,email',
                'password'              => 'required|string|min:6|confirmed',
                'role'                  => 'required|in:admin,manager,user',
            ]);

            // Create the user (password is auto-hashed via User model cast)
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => $validated['password'],
                'role'     => $validated['role'],
            ]);

            // Create a Sanctum token for this user
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully.',
                'data'    => [
                    'user'  => $user,
                    'token' => $token,
                ],
            ], 201);

        } catch (ValidationException $e) {
            // Return validation errors in clean JSON format
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Login an existing user.
     */
    public function login(Request $request)
    {
        try {
            // Validate login fields
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            // Attempt authentication
            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials. Email or password is wrong.',
                ], 401);
            }

            // Get the authenticated user
            $user = Auth::user();

            // Create Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'data'    => [
                    'user'  => $user,
                    'token' => $token,
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Logout the current user (revoke their token).
     */
    public function logout(Request $request)
    {
        try {
            // Delete the current user's token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed.',
            ], 500);
        }
    }

    /**
     * Get the currently authenticated user's info.
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data'    => $request->user(),
        ]);
    }
}
