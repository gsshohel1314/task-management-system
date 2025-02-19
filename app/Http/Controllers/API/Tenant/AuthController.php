<?php

namespace App\Http\Controllers\API\Tenant;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\API\BaseController;

class AuthController extends BaseController
{
    public function register(Request $request)
    {        
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $success['token'] = $user->createToken('auth_token')->plainTextToken;
        $success['token_type'] = 'Bearer';

        return $this->sendResponse($success, 'Tenant user registered successfully.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $success['token'] = $user->createToken('auth_token')->plainTextToken;
            $success['token_type'] = 'Bearer';

            return $this->sendResponse($success, 'Tenant user logged in successfully.');
        } else {
            return $this->sendError('Invalid login details.', [], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse([], 'Tenant user logged out successfully.');
    }
}
