<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserCode;
use App\Models\UserRole;
use App\Notifications\TwoFactorCode;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function register(Request $request){
        $validateDate = $request->validate(
            [
                'name' => 'required',
                'email' => 'email|required|unique:users',
                'password' => 'required',
                'role' => 'required|in:support,user'
            ]
        );
        $validateDate['password'] = bcrypt($validateDate['password']);
        $validateDate['login_type'] = "Manual";
        
        $user = User::create($validateDate);
        $role = Role::where('name', $validateDate['role'])->first('id');
        if(isset($role->id)){
            $userRole = [
                'user_id' => $user->id,
                'role_id' => $role->id
            ];
            UserRole::create($userRole);
        }
        
        $user->generateCode($user);

        return response(['message' =>  "Please check your mail, you will get the code for verify account."]);

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

        if(isset(auth()->user()->email_verified_at)){
            $accessToken = auth()->user()->createToken('authToke')->accessToken;
            $auth = auth()->user();
            $userRole = UserRole::where('user_id', $auth->id)->with('role')->first();
            if(isset($userRole->role->name)){
                $auth->role = $userRole->role->name;
            }
        
            
            return response(['user' => $auth, 'access_token' => $accessToken]);            
        } else {
            return response(['message' => 'Please verify email account.']);            
        }
        

    }

    public function verifyCode(Request $request){
        
        $verifyData = $request->validate(
            [
                'email' => 'email|required',
                'user_code' => 'required'
            ]
        );

        $userDetail = User::where('email', $verifyData['email'])->first('id');
        if(isset($userDetail->id)){
            $isUserCode = UserCode::where(['user_id' => $userDetail->id, 'code' => $verifyData['user_code']])->first();
            if(isset($isUserCode)){
                User::where('id',$userDetail->id)->update(['email_verified_at' => Carbon::now()]);
                return response(['message' => 'User Register Success Fully']);
            } else {
                return response(['message' => 'User Code is invalid.']);
            }   
        } else {
            return response(['message' => 'User Email is not vaild.']);
        }
        
    }


}
