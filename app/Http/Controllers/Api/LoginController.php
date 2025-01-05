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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
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
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'otp' => 'required|string|size:6',
    ]);

    // $otpRecord = Otp::where('user_id', $request['user_id'])
    //     ->where('otp', $request['otp'])
    //     ->orderBy('id', 'desc')
    //     ->first();

    $otpRecord = Otp::where('user_id', $request['user_id'])
    ->where('otp', $request['otp'])
    ->where('status', '!=', 'used') 
    ->where(function ($query) {
        $query->whereNull('expiry') 
              ->orWhere('expiry', '>=', Carbon::now()); 
    })
    ->orderBy('id', 'desc') 
    ->limit(1) 
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

    $user = User::findOrFail($request['user_id']);
    $token = auth('api')->login($user);

    return response()->json(['token' => $token]);

    // return $this->createToken($token);
}


    public function resendOtp(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
    
        $otpTime = Config::get('otp.otp_time', env('OTP_TIME', 1)); 
    
        $otpRecord = Otp::where('user_id', $validated['user_id'])
            ->orderBy('id', 'desc') 
            ->first();
            // dd($otpRecord); 
        if (($otpRecord && Carbon::now()->diffInMinutes($otpRecord->updated_at)) < $otpTime) {
            return response()->json([
                'message' => "Please wait before requesting a new OTP. Try again in " . ($otpTime - Carbon::now()->diffInMinutes($otpRecord->updated_at)) . " minute(s).",
            ], 429); 
        }
        $newOtp = mt_rand(100000, 999999);

        if ($otpRecord) {
            $otpRecord->update([
                'otp' => $newOtp,
                'status' => 'pending',
                'expiry' => Carbon::now()->addMinutes(10), 
                'updated_at' => now(),
            ]);
        } else {
            Otp::create([
                'user_id' => $validated['user_id'],
                'otp' => $newOtp,
                'status' => 'pending',
                'expiry' => Carbon::now()->addMinutes(10), 
                'complete' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
        $this->sendOtpToUser($validated['user_id'], $newOtp);
    
        return response()->json([
            'message' => 'A new OTP has been sent successfully.',
        ], 200);
    }
    
    protected function sendOtpToUser($userId, $otp)
    {
        $user = \App\Models\User::find($userId);
        if ($user) {
            \Log::info("OTP for user {$user->id}: $otp");
        }
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
