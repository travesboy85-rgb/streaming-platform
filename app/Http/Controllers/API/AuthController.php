<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    // ✅ Register new user/creator
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|string|in:user,creator' // only allow user or creator
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Ensure role exists before assigning
        Role::firstOrCreate(['name' => $request->role, 'guard_name' => 'web']);
        $user->assignRole($request->role);

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->load('roles');

        return response()->json([
            'status'  => 'success',
            'message' => 'User registered successfully',
            'user'    => $user,
            'roles'   => $user->getRoleNames(),
            'token'   => $token
        ], 201);
    }

    // ✅ Login with Sanctum token
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->load('roles');

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful',
            'user'    => $user,
            'roles'   => $user->getRoleNames(),
            'token'   => $token
        ]);
    }

    // ✅ Logout (revoke current token)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    // ✅ Logout all tokens
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out from all devices'
        ]);
    }

    // ✅ Get authenticated user
    public function user(Request $request)
    {
        $user = $request->user();
        $user->load('roles');

        return response()->json([
            'status' => 'success',
            'user'   => $user,
            'roles'  => $user->getRoleNames()
        ]);
    }

    // ✅ Admin: list all users
    public function allUsers()
    {
        $users = User::with('roles')->get();

        return response()->json([
            'status' => 'success',
            'users'  => $users
        ]);
    }

    // ✅ Admin: delete a user
    public function deleteUser($id)
    {
        $user = User::find($id);
        if (! $user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'User deleted successfully',
            'user'    => $user
        ]);
    }
}
