<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\TwoFactorCode;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function register(Request $request){
        $validateDate = $request->validate(
            [
                'name' => 'required',
                'email' => 'email|required|unique:users',
                'password' => 'required'
            ]
        );
        $validateDate['password'] = bcrypt($validateDate['password']);
        $validateDate['login_type'] = "Manual";

        $user = User::create($validateDate);

        $accessToken = $user->createToken('authToke')->accessToken;

        return response(['user' => $user, 'access_token' => $accessToken]);

    }

    public function login(Request $request){

        $loginData = $request->validate(
            [
                'email' => 'email|required',
                'password' => 'required'
            ]
        );

        if(! auth()->attempt($loginData)){
            return response(['message' => 'Invalid Credentials']);
        }

        $accessToken = auth()->user()->createToken('authToke')->accessToken;
        return response(['user' => auth()->user(), 'access_token' => $accessToken]);
    }


}
