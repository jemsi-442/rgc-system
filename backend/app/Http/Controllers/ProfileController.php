<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()?->load('roles');

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }
}
