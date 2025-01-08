<?php

namespace App\Http\Controllers;

use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'description' => 'required|string',
            'website' => 'required|string|url|max:255',
        ]);
    
        // Créer un utilisateur de type entreprise
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'enterprise',
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
    
        // Créer l'entreprise associée
        $user->enterprise()->create([
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'description' => $request->description,
            'website' => $request->website,
        ]);
    
        return redirect()->route('login')
            ->with('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            
            // Redirection selon le type d'utilisateur
            if ($user->type === 'admin') {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Bienvenue dans votre espace administrateur !');
            } elseif ($user->type === 'enterprise') {
                return redirect()->route('enterprise.dashboard')
                    ->with('success', 'Bienvenue dans votre espace entreprise !');
            }
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Ces identifiants ne correspondent pas à nos enregistrements.',
            ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
