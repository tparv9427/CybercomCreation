<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('reporting-token')->plainTextToken;

        AuditLog::log('login', ['email' => $user->email]);

        return response()->json([
            'user'  => $user->load('roles'),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        AuditLog::log('logout', ['email' => $user->email]);

        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function updateConfig(Request $request)
    {
        $user = $request->user();
        if ($request->has('column_config')) {
            $user->column_config = $request->get('column_config');
        }
        if ($request->has('default_view_id')) {
            $user->default_view_id = $request->get('default_view_id');
        }
        $user->save();

        return response()->json([
            'message' => 'Config updated successfully',
            'user' => $user->load('roles')
        ]);
    }
}
