<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $rules = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'nama_pengguna' => 'required|string',
            'nama_lengkap' => 'required|string',
            'confirm_password' => 'required|string|min:8|same:password',
        ]);

        $rules['password'] = bcrypt($rules['password']);

        $user = User::create($rules);

        return response([
           'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken
        ]);
    }

    public function login(Request $request)
    {
        $rules = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if(!Auth::attempt($rules))
        {
            return response([
                'message' => 'Invalid Credentials'
            ], 403);
        }

        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'Logged out'
        ], 200);
    }

    public function user()
    {
        return response([
            'user' => auth()->user()
        ], 200);
    }
}
