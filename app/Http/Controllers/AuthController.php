<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Client;

class AuthController extends Controller
{
    public function login(){
        return view('auth.login');
    }

    public function check(Request $request){
        $credentials = $request->validate([
            'user' => 'required',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials)){
            $request->session()->regenerate();

            $user = auth()->user();

            if(auth()->user()->isRole('admin')){

                return redirect()->intended('/');

            }elseif(auth()->user()->isRole('counter')){

                return redirect()->route('attendances.index');

            }elseif(auth()->user()->isRole('trainer')){

                return redirect()->route('routines.create');

            }elseif(auth()->user()->isRole('client')){

                return redirect()->route('index_client');

            }elseif(auth()->user()->isRole('sales')){

                return redirect()->route('clients.index');

            }
            

        }else{
            $client = Client::active()->where('document', $request->user)->first();
            if($client && $request->password == $client->password){
                $user = User::where('user', 'alumno')->first();
                if($user){
                    Auth::login($user);
                    $request->session()->regenerate();
                    session()->put('client', $client);
                    return redirect()->route('index_client');
                }
            }
        }

        return back()->withErrors([
            'user' => 'Usuario o contraseña incorrecta'
        ]);
    }

    public function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
