<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ],[
            'email.required' => 'O email não foi informado', 
            'email.email'    => 'Digite seu email válido', 
            'password.required' => 'A senha é obrigatória'
        ]);


        $user = User::where('email', $request->email)->first();

        if( $user  && Hash::check( $request->password, $user->password) ){

            Auth::login($user);
            return redirect()->intended('/produtos');

        }
        return back()->withErrors([
                    'email' => 'Credenciais inválidas.'
        ])->withInput();
    }

    public function logout(Request $request){
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }


}
