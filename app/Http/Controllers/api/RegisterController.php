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
        // Validate the request
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:8',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:1024',
        ]);
    
        // Return errors if validation fails
        if ($validator->fails()) {
            return response()->json([
                'isSuccess' => false,
                'error' => ['message' => $validator->errors()],
                'data' => [],
            ], 401);
        }
    
        // Handle file upload after successful validation
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = public_path('uploads/profile');
    
            // Ensure directory exists
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0777, true);
            }
    
            // Move file to destination
            $image->move($imagePath, $imageName);
        }/*  else {
            return response()->json([
                'isSuccess' => false,
                'error' => ['message' => ['image' => ['The image failed to upload.']]],
                'data' => [],
            ], 400);
        } */
    
        // Create the user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email, // Ensure email is present in request
            'password' => bcrypt($request->password),
            'image' => 'uploads/profile/' . $imageName, // Save image path in DB
        ]);
    
        // Generate API token
        $token = $user->createToken('auth_token')->plainTextToken;
    
        // Notify user
        createUserNotification(
            $user->id,
            1, 
            'Welcome to our platform, ' . $user->first_name . '!',
            ['registered_at' => now()]
        );
    
        // Return success response
        return response()->json([
            'isSuccess' => true,
            'error' => ['message' => 'Registration created successfully'],
            'user' => $user,
            'token' => $token,
        ], 200);
    }
    


    public function register040425(Request $request)
    {
        
        $validator = Validator::make($request->all(),[
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'phone' => 'required|phone|unique:users',
        'password' => 'required|string|min:8',
        'image' => ['required', 'file', 'mimes:jpeg,png,jpg,gif', 'max:1024'],
        ]);

        $imageName = time() . '.' . $request->file('image')->extension();
       $a= $request->file('image')->move(public_path('uploads/profile'), $imageName);
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
            ['password'=> bcrypt($request->password)],
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
