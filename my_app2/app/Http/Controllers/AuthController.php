<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register (Request $request) {
       $fields =  $request-> validate(
            [
                'username'=> ['required', 'max:255'],
                'email'=> ['required', 'max:255', 'email','unique:users'],
                'password'=> ['required', 'min:3', 'confirmed'],
            ]
        );

        $user = User::create($fields);
        Auth::login($user);

        return redirect()->route('home');
    }

    public function login(Request $request) {
        $fields =  $request-> validate(
            [
                'email'=> ['required', 'max:255', 'email',],
                'password'=> ['required',],
            ]
        );
        if(Auth::attempt($fields,$request->remember)) {
            return redirect()->intended('dashboard');
        } else{
            return back()->withErrors([
                'failed'=> "The provided credentials do not match our records"
            ]);
        }
    }

    public function logout(Request $request) {
        Auth::logout();  #Deconnecte l'utilisateur 

        #efface complètement les données de session pour empêcher toute réutilisation (ex. : historique de navigation, panier, etc.).
        $request->session()->invalidate(); 
        $request->session()->regenerateToken();

        ##crée un nouveau token CSRF (Cross-Site Request Forgery token).Cela évite que des attaquants puissent exploiter l'ancien token CSRF pour envoyer des requêtes malveillantes au serveur.
        
        return redirect('/');
    }



}
