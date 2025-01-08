<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class GoogleAuthController extends Controller
{
    public function redirect() {
        return Socialite::driver('google')->redirect(); 
        ##L'utilisateur est redirigé vers Google pour s'authentifier.Cela est géré par la méthode redirect().
        #Une fois que l'utilisateur accepte de partager ses informations, Google redirige vers votre application.
    }

    public function callback() {
        #Récupère les informations de l'utilisateur.
        $googleUser = Socialite::driver('google')->user();  


        #Enregistre ou met à jour l'utilisateur dans la base de données.
    $user = User::updateOrCreate(
        ['google_id'=> $googleUser->id],
        [
            'name'=>$googleUser->name,
            'email'=>$googleUser->email,
            'password'=>Str::password(12),
            'email_verified_at'=> now(),
        ]

    );

    #Connecte l'utilisateur dans votre application.
    Auth::login($user);
    return redirect()->route('home'); #Redirige l'utilisateur vers une page spécifique.
    }
}
