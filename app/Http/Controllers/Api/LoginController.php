<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Validator;

class LoginController extends Controller
{
    
    public function login(Request $request)
{
    $request->validate([
        'phone' => 'required|digits:10', 
        'password' => 'required|min:6', 
    ]);

    $user = User::where('phone', $request->phone)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid mobile number or password.',
        ], 401);
    }

    $otp = rand(100000, 999999);

    Otp::create([
        'user_id' => $user->id,
        'otp' => $otp,
        'status' => 'pending',
        'expiry' => Carbon::now()->addMinutes(10),
        'complete' => false,
    ]);

    Log::info("OTP for user {$user->phone}: {$otp}");
    return response()->json([
        'message' => 'Login successful. OTP has been sent to your registered mobile number.',
    ]);
}


    public function validateOtp(Request $request)
    {

        $user = Auth();

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required|string|size:6',
        ]);

        $otpRecord = Otp::where('user_id', $request['user_id'])
        ->where('otp', $request['otp'])
        ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid OTP.'], 400);
        }

        if ($otpRecord->expiry && Carbon::now()->greaterThan($otpRecord->expiry)) {
            return response()->json(['message' => 'OTP has expired.'], 400);
        }

        if ($otpRecord->status === 'used') {
            return response()->json(['message' => 'OTP has already been used.'], 400);
        }
    
        $otpRecord->update([
            'status' => 'used',
            'complete' => true,
        ]);
        return response()->json(['message' => 'OTP validated successfully.'], 200);    
    }

    public function createToken($token)
    {
        return response()->json([
            'access_token'=>$token,
            'token_type' => 'bearer',
            'expires_in'=>auth()->factory()->getTTL()*60,
            'user'=>auth()->user()
        ]);
    }

}
