<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * AuthController - Handles user authentication and token management
 *
 * Provides endpoints for:
 * - User login with email/password
 * - Token generation and management
 * - Logout and token revocation
 * - Current user information
 *
 * @group Authentication
 * @authenticated
 */
class AuthController extends Controller
{
    /**
     * Login user and generate API token
     *
     * Authenticates user credentials and returns an API token
     * for subsequent authenticated requests.
     *
     * @bodyParam email string required User email address. Example: admin@hamid.com
     * @bodyParam password string required User password (min 6 characters). Example: password123
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Login successful",
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "name": "Admin User",
     *       "email": "admin@hamid.com",
     *       "role": {"id": 1, "name": "Admin"},
     *       "is_active": true,
     *       "last_login": "2026-04-23T10:30:00Z"
     *     },
     *     "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
     *   }
     * }
     *
     * @response 401 {
     *   "success": false,
     *   "message": "Invalid email or password"
     * }
     *
     * @response 422 {
     *   "success": false,
     *   "message": "Validation failed",
     *   "errors": {"email": ["Email is required"], "password": ["Password is required"]}
     * }
     */
    public function login(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|max:255',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
        ]);

        // Find user by email
        $user = User::where('email', $validated['email'])->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password',
            ], 401);
        }

        // Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This account is inactive. Please contact administrator.',
            ], 403);
        }

        // Generate unique API token
        $token = Str::random(80);
        $user->update([
            'api_token' => $token,
            'api_token_expires_at' => now()->addDays(30), // Token expires in 30 days
            'last_login' => now(),
        ]);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ? [
                        'id' => $user->role->id,
                        'name' => $user->role->name,
                    ] : null,
                    'is_active' => $user->is_active,
                    'last_login' => $user->last_login,
                ],
                'token' => $token,
            ],
        ], 200);
    }

    /**
     * Get current authenticated user
     *
     * Returns information about the currently authenticated user
     * from the provided API token.
     *
     * @queryParam token string required API token in header or query parameter. Example: 8V3xK9mL2pQzJ5vN7...
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Current user",
     *   "data": {
     *     "id": 1,
     *     "name": "Admin User",
     *     "email": "admin@hamid.com",
     *     "role": {"id": 1, "name": "Admin"},
     *     "is_active": true,
     *     "phone": "+966501234567",
     *     "address": "Riyadh, Saudi Arabia",
     *     "permissions": ["create-user", "edit-user", "delete-user"]
     *   }
     * }
     *
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated"
     * }
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Get user permissions
        $permissions = [];
        if ($user->role) {
            $permissions = $user->role->permissions()->pluck('name')->toArray();
        }

        return response()->json([
            'success' => true,
            'message' => 'Current user',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ? [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                ] : null,
                'is_active' => $user->is_active,
                'phone' => $user->phone,
                'address' => $user->address,
                'permissions' => $permissions,
            ],
        ], 200);
    }

    /**
     * Logout user and revoke token
     *
     * Revokes the current API token, logging out the user
     * and making future requests with this token invalid.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Logout successful"
     * }
     *
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated"
     * }
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Revoke token by clearing it
        $user->update(['api_token' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ], 200);
    }

    /**
     * Refresh API token
     *
     * Generates a new API token for the current user,
     * invalidating the old one for security rotation.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Token refreshed successfully",
     *   "data": {
     *     "token": "newToken9mK3pL5vN7..."
     *   }
     * }
     *
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated"
     * }
     */
    public function refresh(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Generate new token and extend expiration
        $newToken = Str::random(80);
        $user->update([
            'api_token' => $newToken,
            'api_token_expires_at' => now()->addDays(30), // Extend expiration to 30 days
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'token' => $newToken,
            ],
        ], 200);
    }

    /**
     * Change user password
     *
     * Allows authenticated user to change their password.
     * Must provide current password for verification.
     *
     * @bodyParam current_password string required Current password for verification. Example: oldPassword123
     * @bodyParam new_password string required New password (min 6 characters, different from current). Example: newPassword456
     * @bodyParam password_confirmation string required New password confirmation. Example: newPassword456
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Password changed successfully"
     * }
     *
     * @response 401 {
     *   "success": false,
     *   "message": "Current password is incorrect"
     * }
     *
     * @response 422 {
     *   "success": false,
     *   "message": "Validation failed",
     *   "errors": {
     *     "new_password": ["New password must be different from current password"]
     *   }
     * }
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Validate password change request
        $validated = $request->validate([
            'current_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Current password is required',
            'new_password.required' => 'New password is required',
            'new_password.confirmed' => 'Password confirmation does not match',
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 401);
        }

        // Check if new password is different
        if (Hash::check($validated['new_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'new_password' => ['New password must be different from current password'],
                ],
            ], 422);
        }

        // Update password
        $user->update(['password' => Hash::make($validated['new_password'])]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ], 200);
    }
}
