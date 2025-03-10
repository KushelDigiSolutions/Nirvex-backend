<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    // public function __contruct()
    // {
    //     $this->middleware('auth.api',['except'=>['login','register']]);
    // }


    public function getUser(){
        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'phone' => 'required|phone|unique:users',
        'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'isSuccess' => false,
                'error' => ['message' =>$validator->errors()],
                'data' => [],
            ], 401);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['email'=> $request->email],
            ['password'=> bcrypt($request->password)]
        ));


        $token = $user->createToken('auth_token')->plainTextToken;

        createUserNotification(
            $user->id,
            1, 
            'Welcome to our platform, ' . $user->name . '!',
            ['registered_at' => now()]
        );

        return response()->json(['isSuccess'=>true,
           'error' => ['message' => 'Registration created successfully'],
            'user' => $user,
            'token' => $token,
             200]);
    }

    // public function createToken($token)
    // {
    //     return response()->json([
    //         'access_token'=>$token,
    //         'token_type' => 'bearer',
    //         'expires_in'=>auth()->factory()->getTTL()*60,
    //         'user'=>auth()->user()
    //     ]);
    // }
}
