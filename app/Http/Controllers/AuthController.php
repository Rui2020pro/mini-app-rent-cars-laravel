<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    // next criar um utilizador na base de dados- ir bd e criar um user
    /*
    php artisan tinker

        $user = new App\Models\User
        $user->name = 'WideWolf'
        $user->email = 'example@gmail.com'
        $user->password = bcrypt('1234')
        var_dump($user->getAttributes())
        $user->save() // true

    */
    public function login(Request $request){
        // autenticação login (email e password)
        // dd($request->all(['email' , 'password']));
        
        // attempt() - realiza uma tentativa de autenticação
        // auth espera ou web ou api
        $token = auth('api')->attempt(['email' => $request->email, 'password' => $request->password]);
        // dd($token);

        if($token){
            return response()->json(['token' => $token], 200);
        }else {
            return response()->json(['erro' => 'Email ou Password inválidos'], 403);
            // 403 - forbidden() - proibido(Login Inválido)
            // 401 - Unauthorizated - Não Autorizado // next step app/Http/kernel.php
        }

        // retornar um JSON Web Token

    }
    public function logout(){
        // return 'logout';
        auth('api')->logout(); // cliente encaminhar um jwt valido
        return response()->json(['msg' => 'Logout realizado com sucesso']);
    }
    public function refresh(){
        // return 'refresh';
        $token = auth('api')->refresh(); // cliente encaminhar um jwt valido
        return response()->json(['token' => $token]);
    }
    public function me(){
        // dd(auth()->user()->getAttributes());
        return response()->json(auth()->user(), 200); // next step components/login.blade...
    }
}
