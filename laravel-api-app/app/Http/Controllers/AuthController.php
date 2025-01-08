<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $user = User::create($request->validated());
        $token = $user->createToken($request->name);
        return [
            'user'=>$user,
            'token'=>$token->plainTextToken,
        ];
    }
    public function login(LoginRequest $request){
        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return [
                'message' => 'Email ou mot de passe incorrects'
            ];
        }
        $token = $user->createToken($user->name);
        return [
            'user'=>$user,
            'token'=>$token->plainTextToken,
        ];
    }
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return [
            'message' => 'Vous etes maintenant déconnecté'
        ];
    }
}
