<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class EcommerceApiController extends Controller
{
    public function createAddress(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'phone' => 'required|string|max:15',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'address_type' => 'required|integer|in:0,1', // 0: Home, 1: Office
            'pincode' => 'required|string|max:10',
        ]);

        $user = auth()->user();

        $address = Address::create([
            'user_id' => $user->id,
            'name' => $validatedData['name'],
            'address1' => $validatedData['address1'],
            'address2' => $validatedData['address2'] ?? null,
            'landmark' => $validatedData['landmark'] ?? null,
            'phone' => $validatedData['phone'],
            'city' => $validatedData['city'],
            'state' => $validatedData['state'],
            'address_type' => $validatedData['address_type'],
            'pincode' => $validatedData['pincode'],
            'status' => 1, 
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address created successfully.',
            'data' => $address,
        ], 201);
    }

    public function getAddressesCustomer(Request $request)
    {
        $user = auth()->user();
    
        // Commented out, so we define a default value to prevent errors
        $defaultAddressId = null;
    
        $addresses = \App\Models\Address::where('user_id', $user->id)
            ->where('status', 1)
            ->get()
            ->map(function ($address) use ($defaultAddressId) {
                $address->address_type_label = $address->address_type == 0 ? 'Home' : 'Office';
                
                // Only check default address if $defaultAddressId is not null
                $address->default_address = ($defaultAddressId !== null && $address->id == $defaultAddressId);
    
                return $address;
            });
    
        return response()->json([
            'success' => true,
            'data' => $addresses,
        ], 200);
    }
    

}
