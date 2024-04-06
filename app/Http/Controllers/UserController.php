<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = auth()->user();

        return response()->json([
            'users' => $users,
            'status' => 200
        ]);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['user' => $user], 201);
    }

    public function loginApi(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {

            $token = $user->createToken('api-token')->plainTextToken;

            return response([
                'message' => 'Login Success',
                'status' => 'success',
                'token' => $token,
                'user' => $user,
            ], 200);
        }

        return response([
            'message' => 'Invalid credentials.',
            'status' => 'failed',
        ], 401);
    }

    // Optional: Method to handle user logout via API
    public function logoutApi(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

}