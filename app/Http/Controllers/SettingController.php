<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;

class SettingController extends Controller
{
    public function index(){
        return view('settings');
    }

    public function update(Request $request){
        $request->validate([
            'password' => 'required',
            'new_password' => 'required|min:5'
        ]);

        if(!Hash::check($request->password, auth()->user()->password)){
            return back()->withErrors([
                'password' => 'La contraseña actual no coincide'
            ]);
        }

        auth()->user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        session()->flash('message', 'Ajustes guardados');

        return redirect()->route('settings.index');
    }

    public function index_client(){
        return view('settings_client');
    }

    public function update_client(Request $request){
        $request->validate([
            'password' => 'required',
            'new_password' => 'required|min:5'
        ]);

        $client = session('client') ? session('client') : null;
        $client = Client::find($client->id);

        if($request->password != $client->password){
            return back()->withErrors([
                'password' => 'La contraseña actual no coincide'
            ]);
        }

        $client->update([
            'password' => $request->new_password
        ]);

        session()->flash('message', 'Ajustes guardados');

        return redirect()->route('settings_client.index');
    }
}
