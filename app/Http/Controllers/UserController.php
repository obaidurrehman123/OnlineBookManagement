<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function signUp(Request $request) {
        $userValidation = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);
        if($userValidation->fails()){
            return response()->json(['success'=>false , 'message'=>'user validation failing'],422);
        }
        $userData = $userValidation->validated();
        //dump($userData);
        $user = User::create([
            'username' => $userData['username'],
            'email' => $userData['email'],
            'password' => bcrypt($userData['password'])
        ]);
        return response()->json(['success'=>true , 'message'=>'successfully registered the user'],200);
    }

    public function signIn(Request $request){
        $checkCredentials = $request->only('email', 'password');
        if ($token = Auth::guard('api')->attempt($checkCredentials)) {
            return response()->json(['success' => true, 'token' => $token], 200);
        }
        return response()->json(['success' => false, 'message' => 'Wrong credentials'], 401);
    }
}
