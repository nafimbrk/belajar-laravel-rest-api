<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        // dd($user);

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        return $user->createToken('user login')->plainTextToken;
    }

    function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
    }

    function me(Request $request) {

        // misal saja mendapatkan data dmn pemiliknya adl user yg lagi login
        // $user = Auth::user();
        // $post = Post::where('user', $user->id);

        return response()->json(Auth::user());
    }
}
