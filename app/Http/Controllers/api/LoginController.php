<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\Otp;
use App\Models\Address;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{

public function login(Request $request)
{
    try {
        $request->validate([
            'phone' => 'required|digits:10', 
            'password' => 'required|min:6', 
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => 'Validation errors occurred',
                'details' => $e->errors(),
            ],
            'data' => [],
        ], 422);
    }

     $user = User::where('phone', $request->phone)->first();
    // $usersWithRoles = User::role(['customer', 'seller'])->get();

    // $user = User::where('phone', $request->phone)
    //         ->whereHas('roles', function ($query) {
    //             $query->whereIn('name', ['customer', 'seller']);
    //         })
    //         ->first();

    // echo '<pre>';  print_r($usersWithRoles); die;

    if (!$user) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => 'User not found or does not have the required role.',
            ],
            'data' => [],
        ], 404); 
    }
    
    $user->makeHidden(['created_at', 'updated_at']);

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => 'Invalid mobile number or password.',
            ],
            'data' => [],
        ], 401);
    }

    $otp = rand(100000, 999999);
    $this->sendSms($request->phone, $otp);
    Otp::create([
        'user_id' => $user->id,
        'otp' => $otp, 
        'status' => 'pending',
        'expiry' => Carbon::now()->addMinutes(10),
        'complete' => false,
    ]);

    Log::info("OTP for user {$user->phone}: {$otp}");

    return response()->json([
        'isSuccess' => true,
        'errors' => [
            'message' => 'Login successful. OTP has been sent to your registered mobile number.',
        ],
        'data' => [
            // 'message' => 'Login successful. OTP has been sent to your registered mobile number.',
            'data' => $user
        ],
    ]);
}


public function sendSms($phone, $otp)
{
    $url = 'http://37.59.76.46/api/mt/SendSMS';

    // Build the SMS message using the OTP
    $message = "Dear Customer, your OTP to access your Nirviex Real Estate account is: {$otp} It will expire in 10 minutes. If you did not request this, please contact support at Info@nirviex.com";

    // Define the query parameters. Consider moving sensitive values to your .env file.
    $params = [
        'user'           => 'Nirviex',
        'password'       => 'q12345',
        'senderid'       => 'NRVIEX', // Remove space if not intended
        'channel'        => 'Trans',
        'DCS'            => 0,
        'flashsms'       => 0,
        'number'         => $phone,
        'text'           => $message,
        'DLTTemplateId'  => '1707173564539573448',
        'TelemarketerId' => '12071651285xxxxxxx',
        'Peid'           => '1701173553742338688',
        'route'          => '06'
    ];

    // Make the HTTP GET request
    $response = Http::get($url, $params);

    if ($response->successful()) {
        return $response->body(); // Process response as needed
    } else {
        // Log or handle error appropriately
        return response()->json(['error' => 'Failed to send SMS'], $response->status());
    }
}
    
    public function login27012025(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|digits:10', 
                'password' => 'required|min:6', 
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors occurred',
                'errors' => $e->errors(),
            ], 422);
        }
    
        $user = User::where('phone', $request->phone)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid mobile number or password.',
            ], 401);
        }
    
        $otp = rand(100000, 999999);
    
        Otp::create([
            'user_id' => $user->id,
            'otp' => '999999',
            'status' => 'pending',
            'expiry' => Carbon::now()->addMinutes(10),
            'complete' => false,
        ]);
    
        Log::info("OTP for user {$user->phone}: {$otp}");
    
        return response()->json([
            'success' => true,
            'message' => 'Login successful. OTP has been sent to your registered mobile number.',
        ]);
    }
    


public function validateOtp(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'otp' => 'required|string|size:6',
    ]);

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
        return response()->json(['isSuccess' => false,
                            'error' => ['message' => 'Invalid OTP.'],
                            'data' => [], ], 401);
    }

    if ($otpRecord->expiry && Carbon::now()->greaterThan($otpRecord->expiry)) {
        return response()->json(['isSuccess' =>false, 
                            'error' => ['message' => 'OTP has expired.'], 
                            'data' => [],
                        ], 401);
    }

    if ($otpRecord->status === 'used') {
        return response()->json(['isSuccess' =>false,
                            'error' => ['message' => 'OTP has already been used.'],
                            'data' => [], 
                        ], 401);
    }

    $otpRecord->update([
        'status' => 'used',
        'complete' => true,
    ]);

    $user = User::findOrFail($request['user_id']);
    $user->makeHidden(['created_at', 'updated_at']);
    $token = auth('api')->login($user);

     $addresses = Address::where('user_id', $user->id)->get();

    // Generate authentication token
    // $token = auth('api')->login($user);

    return response()->json([
        'isSuccess' => true,
        'error' => ['message' => 'Login Successfully'],
        'data' => [
            'user' => $user,
            'addresses' => $addresses, // Include addresses in response
        ],
        'token' => $token,
    ], 200);

    // return response()->json(['isSuccess' => true, 'error' => ['message' => 'Login Successfully'],
    // 'data' =>$user, 'token' => $token],200);

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
            return response()->json(['isSuccess' =>false, 
               'error' => ['message' => "Please wait before requesting a new OTP. Try again in "
                . ($otpTime - Carbon::now()->diffInMinutes($otpRecord->updated_at)) . " minute(s)."],
                'data' =>[],
            ], 401); 
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
    
        return response()->json(['isSuccess' =>true,
            'error'=> ['message' => 'A new OTP has been sent successfully.'],
            'data' =>$otpRecord, 
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|digits:10'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Validation errors occurred',
                    'details' => $e->errors(),
                ],
                'data' => [],
            ], 422);
        }

        $user = User::where('phone', $request->phone)->first();
        // $usersWithRoles = User::role(['customer', 'seller'])->get();
    
        // $user = User::where('phone', $request->phone)
        //         ->whereHas('roles', function ($query) {
        //             $query->whereIn('name', ['customer', 'seller']);
        //         })
        //         ->first();
    
        // echo '<pre>';  print_r($usersWithRoles); die;
    
        if (!$user) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'User not found or does not have the required role.',
                ],
                'data' => [],
            ], 404); 
        }
        
        $user->makeHidden(['created_at', 'updated_at']);
    
        if (!$user) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Invalid mobile number',
                ],
                'data' => [],
            ], 401);
        }
    
        $otp = rand(100000, 999999);
        $this->sendSms($request->phone, $otp);
        Otp::create([
            'user_id' => $user->id,
            'otp' => $otp, 
            'status' => 'pending',
            'expiry' => Carbon::now()->addMinutes(10),
            'complete' => false,
        ]);
    
        Log::info("OTP for user {$user->phone}: {$otp}");
    
        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'OTP has been sent to your mobile number.',
            ],
            'data' => [
                // 'message' => 'Login successful. OTP has been sent to your registered mobile number.',
                'data' => $user
            ],
        ]);
    
    }
    
    public function updatePassword(Request $request)
    {
        // Validate the request data
        $request->validate([
            'password' => 'required|string|min:8',
            'reset_password' => 'required|string|same:password',
        ]);

        // Get authenticated user from token
        $user = auth('api')->user();
        
        if (!$user) {
            return response()->json([
                'isSuccess' => false,
                'error' => ['message' => 'Unauthorized'],
                'data' => [],
            ], 401);
        }

        // Update user's password
        $user->password = bcrypt($request['password']);
        $user->save();

        // Return simplified success response
        return response()->json([
            'isSuccess' => true,
            'error' => ['message' => ''],
            'data' => ['message' => 'Password reset successfully'],
        ], 200);
    }

    
    
    protected function sendOtpToUser($userId, $otp)
    {
        $user = \App\Models\User::find($userId);
        if ($user) {
            \Log::info("OTP for user {$user->id}: $otp");
        }
    }

    public function updatePincode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'pincode' => 'required|digits:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'isSuccess' => false,
               'error' => ['message' => $validator->errors()],
               'data' => [],
            ], 401);
        }
        $validated = $validator->validated();
    
        $user = \App\Models\User::find($validated['id']);
        $user->pincode = $validated['pincode'];
        $user->save();
    
        return response()->json([
            'isSuccess' => true,
            'error' => ['message' => 'Pincode updated successfully'],
            'data' => $user,
        ]);
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
