<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'member',
        ]);

        $token = auth('api')->login($user);

        return response()->json([
            'status' => 'success',
            'user'   => $user,
            'token'  => $token,
            'type'   => 'bearer'
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email or password'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'user'   => auth('api')->user(),
            'token'  => $token,
            'type'   => 'bearer'
        ]);
    }

    // PROFILE
    public function profile()
    {
        return response()->json(auth('api')->user());
    }

    // LOGOUT
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    // REFRESH TOKEN
    public function refresh()
    {
        return response()->json([
            'token' => auth('api')->refresh(),
            'type'  => 'bearer'
        ]);
    }
}
