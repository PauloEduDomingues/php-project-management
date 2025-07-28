<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'password' => 'required|string|min:3'
        ]);

        $user = User::create($validated);
        $user->assignRole('colaborator');
        $token = $user->createToken('api-token');
        return response()->json([
            'status' => 'Authenticated',
            'user' => $user->name,
            'token' => $token->plainTextToken
        ], 201);
    }

    public function login (Request $request) {
        $validated = $request->validate([
            'email' => 'required|email:rfc,dns',
            'password' => 'required|string|min:3'
        ]);

        if (Auth::attempt($validated)) {
            $user = User::where('email', $validated['email'])->first();
            $token = $user->createToken('api-token');
            return response()->json([
                'status' => 'Authenticated',
                'user' => $user->name,
                'token' => $token->plainTextToken
            ], 200);
        }

        return response()->json(['message'=>'Invalid credentials'],401);
    }

    public function logout (Request $request) {
        $token = $request->bearerToken();
        $access_token = PersonalAccessToken::findToken($token);
        $access_token->delete();
        return response()->json([
            'message'=>'logout!'
        ], 200);
    }
}
