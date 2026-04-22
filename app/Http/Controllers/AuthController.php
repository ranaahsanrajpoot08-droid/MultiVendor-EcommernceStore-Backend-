<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller {
    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:customer,vendor',
            'shop_name' => 'required_if:role,vendor'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($request->role === 'vendor') {
            Vendor::create([
                'user_id' => $user->id,
                'shop_name' => $request->shop_name,
                'description' => $request->description ?? '',
                'status' => 'pending',
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['user' => $user->load('vendor'), 'token' => $token]);
    }

    public function login(Request $request) {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => ['Invalid credentials']]);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['user' => $user->load('vendor'), 'token' => $token]);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request) {
        return response()->json($request->user()->load('vendor'));
    }
}
