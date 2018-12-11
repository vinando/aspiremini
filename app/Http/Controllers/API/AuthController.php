<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    function register(Request $request) {
        $rule = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ];
        $this->validate($request, $rule);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->gender = $request->gender;
        $user->birthdate = $request->birthdate;
        $user->save();

        return response(json_encode(['isSuccess'=>1, 'message'=>'User Registered']), 200)->header('Content-Type', 'application/json');
    }
}
