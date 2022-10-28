<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function signup(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'location' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'location' => $fields['location'],
            'password' => Hash::make($fields['password'])
        ]);
        $token = $user->createToken('gamestoreapp')->plainTextToken;

        return [
            'message' => 'User registered',
            'user' => $user,
            'token' => $token
        ];
    }
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email',  $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return [
                'message' => 'Bad credentials',
            ];
        }
        $token = $user->createToken('gamestoreapp')->plainTextToken;
        return [
            'message' => 'Logged in',
            'user' => $user,
            'token' => $token,
        ];
    }
    public function destroy(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $response = $user->delete();
        $request->user()->currentAccessToken()->delete();

        if ($response == 1) {
            return [
                'message' => 'account deleted',
            ];
        } else {
            return [
                'message' => 'NOT DELETED'
            ];
        }
    }
}
