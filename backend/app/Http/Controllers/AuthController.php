<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:30',
            'region_id' => 'required|exists:regions,id',
            'district_id' => [
                'required',
                Rule::exists('districts', 'id')->where(
                    fn ($query) => $query->where('region_id', $request->input('region_id'))
                ),
            ],
            'church_id' => [
                'required',
                Rule::exists('churches', 'id')->where(
                    fn ($query) => $query
                        ->where('district_id', $request->input('district_id'))
                        ->where('status', 'active')
                ),
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => 'member',
            'region_id' => $validated['region_id'],
            'district_id' => $validated['district_id'],
            'branch_id' => $validated['church_id'],
            'church_id' => $validated['church_id'],
            'status' => 'active',
        ]);

        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles([$user->role]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => $user->load(['roles', 'church.district.region']),
            'token' => $token,
            'type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($validated)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email or password',
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => $user->load(['roles', 'church.district.region']),
            'token' => $token,
            'type' => 'Bearer',
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'user' => $request->user()?->load(['roles', 'church.district.region']),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
        ]);
    }
}
