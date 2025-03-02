<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Address;
use App\Models\CustomerDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class AddressController extends Controller
{

    public function store(Request $request)
    {
        
        // Validate the request
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'phone' => 'required|string|max:12',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'address_type' => 'required|integer|in:0,1',
            'pincode' => 'required|string|max:10',
            'status' => 'required|integer|in:0,1',
        ]);
    
        try {
            DB::beginTransaction();
            
            // Create the address
            $address = Address::create($validated);
    
            // If this address should be default
            if ($request->is_default == 1) {
                CustomerDetail::where('user_id', $request->user_id)
                    ->update(['address_id' => $address->id]);
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Address added successfully'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error adding address: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
    public function destroy($id, Request $request)
    {
        // Check if user is admin
        if (!auth()->user()->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        try {
            // Find address with customer validation
            $address = Address::where('id', $id)
                            ->where('user_id', $request->customer_id)
                            ->firstOrFail();
            
            $address->delete(); // Soft delete
            
            return response()->json([
                'success' => true,
                'message' => 'Address deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found or access denied'
            ], 404);
        }
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'phone' => 'required|string|max:12',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'address_type' => 'required|integer|in:0,1',
            'pincode' => 'required|string|max:10',
            'status' => 'required|integer|in:0,1'
        ]);

        try {
            DB::beginTransaction();
            
            $address = Address::findOrFail($id);
            $address->update($validated);

            if ($request->is_default == 1) {
                CustomerDetail::where('user_id', $address->user_id)
                    ->update(['address_id' => $address->id]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating address: ' . $e->getMessage()
            ], 500);
        }
    }


    public function edit($id)
    {
        try {
            $address = Address::findOrFail($id);
            
            // Get the customer details for this user's default address
            $customerDetail = CustomerDetail::where('user_id', $address->user_id)->first();
            
            // Add is_default flag based on customer_details.address_id
            $address->is_default = ($customerDetail && $customerDetail->address_id == $id) ? 1 : 0;
            
            return response()->json($address);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Address not found'], 404);
        }
    }

    
}