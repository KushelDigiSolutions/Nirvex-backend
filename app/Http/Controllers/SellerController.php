<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Address;
use App\Models\CustomerDetails;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $data = User::where('user_type', 2)
            ->with(['customerDetails.salesOfficer']) 
            ->latest()
            ->get()
            ->map(function ($user) {
               if(!empty($user->customerDetails->salesOfficer) && !empty($user->customerDetails->salesOfficer)){
                $user->so_name = $user->customerDetails->salesOfficer->first_name." / ".$user->customerDetails->salesOfficer->last_name;
                $user->customerDetails->salesOfficer->email;
               }else{
                $user->so_name = 'Seller not assigned';
               }
                return $user;
            });
            return view('admin.sellers.index', [
                'data' => $data,
                'i' => ($request->input('page', 1) - 1) * 5
            ]);
        // return view('admin.sellers.index', compact('data'))
        //     ->with('i', ($request->input('page', 1) - 1) * 5);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        
        $roles = Role::pluck('name', 'name')->all();
        return view('admin.sellers.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try{
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'phone' => ['required', 'digits:10', 'numeric', 'unique:users,phone'],
            'pincode'    => ['required', 'digits:6', 'numeric'],
            'name' => 'required|string',
            'sphone' => ['required', 'digits:10', 'numeric', 'unique:users,phone'],
            'city' => 'required|string',
            'address1' => 'required|string',
            'state'  => 'required|string',
            'status' => 'required|boolean',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e){
        dd($e->errors());
        return redirect()->back()->withErrors($e->errors())->withInput();
    }
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['user_type'] = 2;

        $user = User::create($input);
        if (!$user || !$user->id) {
            return redirect()->back()->with('error', 'User creation failed.');
        }


        $user->assignRole($request->input('roles'));

       Address::create([
            'user_id' =>$user->id,
            'name' =>$request->input('name'),
            'address1' =>$request->input('address1'),
            'address2' =>$request->input('address2'),
            'landmark' =>$request->input('landmark'),
            'phone' =>$request->input('sphone'),
            'city' =>$request->input('city'),
            'state' =>$request->input('state'),
            'address_type' =>$request->input('is_default', 1),
            'pincode' =>$request->input('pincode'),
            'status' =>$request->input('status'),
        ]);

        

        return redirect()->route('sellers.index')->with('success', 'Seller created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($encryptedId): View
    {
        try {
            // Decrypt the ID first
            $id = Crypt::decrypt($encryptedId);
            
            // Query using the decrypted numeric ID
            $user = User::where('id', $id)
                    ->where('user_type', 2)
                    ->firstOrFail();
                    // echo '<pre>'; print_r($user); die;
            return view('admin.sellers.show', compact('user'));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid ID');
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id): View
    {
        $user = User::where('id', decrypt($id))->where('user_type', 2)->firstOrFail();
    //    echo '<pre>'; print_r($user); die;
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        return view('admin.sellers.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'seller_active' => 'required|integer|in:0,1,2',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'customerDetails.shop_name' => 'nullable|string|max:255',
            'customerDetails.shop_owner_name' => 'nullable|string|max:255',
            'customerDetails.gst_number' => 'nullable|string|max:50',
            'customerDetails.pan_number' => 'nullable|string|max:50',
            'addresses' => 'nullable|array',
            'addresses.*.name' => 'required|string|max:255',
            'addresses.*.address1' => 'required|string',
            'addresses.*.address2' => 'nullable|string',
            'addresses.*.city' => 'required|string',
            'addresses.*.state' => 'required|string',
            'addresses.*.pincode' => 'required|string|max:10',
            'addresses.*.phone' => 'required|string|max:20',
            'addresses.*.status' => 'required|boolean',
            'addresses.*.address_type' => 'required|integer|in:0,1',
        ]);
    
        try {
            DB::beginTransaction();
    
            $user = User::findOrFail($id);
    
            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                // Delete old profile image if exists
                if ($user->profile_image && file_exists(public_path('uploads/profile_images/' . $user->profile_image))) {
                    unlink(public_path('uploads/profile_images/' . $user->profile_image));
                }
    
                $image = $request->file('profile_image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/profile_images'), $imageName);
    
                $user->profile_image = $imageName;
            }
    
            // Update User Basic Details
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'seller_active' => $request->seller_active,
            ]);
    
            // Update Customer Details
            if ($user->customerDetails) {
                $user->customerDetails->update([
                    'shop_name' => $request->customerDetails['shop_name'] ?? null,
                    'shop_owner_name' => $request->customerDetails['shop_owner_name'] ?? null,
                    'gst_number' => $request->customerDetails['gst_number'] ?? null,
                    'pan_number' => $request->customerDetails['pan_number'] ?? null,
                ]);
            }
    
            // Update or Create Addresses
            if ($request->has('addresses')) {
                foreach ($request->addresses as $addressData) {
                    $user->addresses()->updateOrCreate(
                        ['id' => $addressData['id'] ?? null], 
                        [
                            'name' => $addressData['name'],
                            'address1' => $addressData['address1'],
                            'address2' => $addressData['address2'] ?? null,
                            'city' => $addressData['city'],
                            'state' => $addressData['state'],
                            'pincode' => $addressData['pincode'],
                            'phone' => $addressData['phone'],
                            'status' => $addressData['status'],
                            'address_type' => $addressData['address_type'],
                        ]
                    );
                }
            }
    
            DB::commit();
            return redirect()->back()->with('success', 'User details updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update user details: ' . $e->getMessage());
        }
    }
    


    public function update15022025(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, ['password']);
        }

        $user = User::where('id', $id)->where('user_type', 2)->firstOrFail();
        $user->update($input);

        // Remove existing roles and assign new ones
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $user = User::where('id', $id)->where('user_type', 2)->firstOrFail();
        $user->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully');
    }

    public function updateSellerActive(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'seller_active' => 'required|in:0,1,2'
        ]);

        // Find the user and update seller_active status
        $user = User::findOrFail($request->id);
        $user->seller_active = $request->seller_active;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Seller active status updated successfully.'
        ]);
    }

    public function updateCustomerSo(Request $request)
    {
        // Validate request data
           // Validate the request
        $request->validate([
            'cs_id' => 'required|exists:users,id', // Ensure the user exists
            'so_id' => 'required|exists:users,id', // Ensure the Sales Officer exists
        ]);

        // Find the CustomerDetails record where user_id matches cs_id
        $customerDetail = CustomerDetails::where('user_id', $request->cs_id)->first();

        if (!$customerDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Customer details not found for the specified user ID.'
            ], 404);
        }

        // Update the so_id in the CustomerDetails table
        $customerDetail->so_id = $request->so_id;
        $customerDetail->save();

        return response()->json([
            'success' => true,
            'message' => 'Sales Officer ID updated successfully for the customer.'
        ]);
    }
    
    public function getSellerActive(Request $request)
    {

        // Find the user and update seller_active status
        $data = User::where('user_type', 2)
                     ->with('roles') // Eager load roles
                     ->get();
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function updateSellerDetails(Request $request)
    {
        // Validate request data
        $request->validate([
            'id' => 'required|exists:users,id',
        ]);
    
        // Find the user
        $user = User::findOrFail($request->id);
    
        // Update only non-empty fields in the `users` table
        if (!empty($request->first_name)) {
            $user->first_name = $request->first_name;
        }
        if (!empty($request->last_name)) {
            $user->last_name = $request->last_name;
        }
        if (!empty($request->email)) {
            $user->email = $request->email;
        }
        if (!empty($request->phone)) {
            $user->phone = $request->phone;
        }
    
        // Save the updated user details
        $user->save();
    
        // Update only non-empty fields in the `customer_details` table
        $customerDetails = CustomerDetails::where('user_id', $user->id)->first();
        if ($customerDetails) {
            if (!empty($request->shop_name)) {
                $customerDetails->shop_name = $request->shop_name;
            }
            if (!empty($request->shop_owner_name)) {
                $customerDetails->shop_owner_name = $request->shop_owner_name;
            }
            if (!empty($request->gst_number)) {
                $customerDetails->gst_number = $request->gst_number;
            }
            if (!empty($request->pan_number)) {
                $customerDetails->pan_number = $request->pan_number;
            }
    
            // Save the updated customer details
            $customerDetails->save();
        }
    
        return response()->json([
            'success' => true,
            'message' => 'User and customer details updated successfully.'
        ]);
    }

    public function updateCustomerShopImage(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shop_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($request->hasFile('shop_image')) {
            // Retrieve the uploaded file
            $file = $request->file('shop_image');
    
            // Generate a unique filename
            $filename = time() . '_' . $file->getClientOriginalName();
    
            // Define the target directory path (inside public/uploads/customers/shop_image)
            $destinationPath = public_path('uploads/customers/shop_image');
    
            // Create the directory if it doesn't exist
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
    
            // Move the uploaded file to the target directory
            $file->move($destinationPath, $filename);
    
            // Update user's shop image path in the database
            $user = User::findOrFail($request->user_id);
    
            if ($user->customerDetails && $user->customerDetails->shop_image) {
                // Delete the old image if it exists
                $oldImagePath = public_path($user->customerDetails->shop_image);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }
    
            // Save the new image path in the database
            $user->customerDetails->update(['shop_image' =>  $filename]);
    
            return response()->json([
                'success' => true,
                'message' => 'Shop image updated successfully!',
                'file_path' => url('uploads/customers/shop_image/' . $filename), // Return full URL of uploaded file
            ]);
        }
    
        return response()->json(['success' => false, 'message' => 'No image uploaded.'], 400);
    }


    public function updateCustomerGstImage(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gst_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('gst_image')) {
            // Retrieve the uploaded file
            $file = $request->file('gst_image');

            // Generate a unique filename
            $filename = time() . '_' . $file->getClientOriginalName();

            // Define the target directory path (inside public/uploads/customers/shop_image)
            $destinationPath = public_path('uploads/customers/gst_image');

            // Create the directory if it doesn't exist
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Move the uploaded file to the target directory
            $file->move($destinationPath, $filename);

            // Update user's shop image path in the database
            $user = User::findOrFail($request->user_id);

            if ($user->customerDetails && $user->customerDetails->gst_image) {
                // Delete the old image if it exists
                $oldImagePath = public_path($user->customerDetails->gst_image);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            // Save the new image path in the database
            $user->customerDetails->update(['gst_image' =>  $filename]);

            return response()->json([
                'success' => true,
                'message' => 'Shop image updated successfully!',
                'file_path' => url('uploads/customers/gst_image/' . $filename), // Return full URL of uploaded file
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No image uploaded.'], 400);
    }
    
    
    public function updateCustomerFssiImage(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fssi_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('fssi_image')) {
            // Retrieve the uploaded file
            $file = $request->file('fssi_image');

            // Generate a unique filename
            $filename = time() . '_' . $file->getClientOriginalName();

            // Define the target directory path (inside public/uploads/customers/shop_image)
            $destinationPath = public_path('uploads/customers/fssi_image');

            // Create the directory if it doesn't exist
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Move the uploaded file to the target directory
            $file->move($destinationPath, $filename);

            // Update user's shop image path in the database
            $user = User::findOrFail($request->user_id);

            if ($user->customerDetails && $user->customerDetails->fssi_image) {
                // Delete the old image if it exists
                $oldImagePath = public_path($user->customerDetails->fssi_image);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            // Save the new image path in the database
            $user->customerDetails->update(['fssi_image' =>  $filename]);

            return response()->json([
                'success' => true,
                'message' => 'Shop image updated successfully!',
                'file_path' => url('uploads/customers/fssi_image/' . $filename), // Return full URL of uploaded file
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No image uploaded.'], 400);
    }


    public function updateCustomerAdharFrontImage(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'adhaar_front_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('adhaar_front_img')) {
            // Retrieve the uploaded file
            $file = $request->file('adhaar_front_img');

            // Generate a unique filename
            $filename = time() . '_' . $file->getClientOriginalName();

            // Define the target directory path (inside public/uploads/customers/shop_image)
            $destinationPath = public_path('uploads/customers/adhaar_front_img');

            // Create the directory if it doesn't exist
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Move the uploaded file to the target directory
            $file->move($destinationPath, $filename);

            // Update user's shop image path in the database
            $user = User::findOrFail($request->user_id);

            if ($user->customerDetails && $user->customerDetails->adhaar_front_img) {
                // Delete the old image if it exists
                $oldImagePath = public_path($user->customerDetails->adhaar_front_img);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            // Save the new image path in the database
            $user->customerDetails->update(['adhaar_front_img' =>  $filename]);

            return response()->json([
                'success' => true,
                'message' => 'Shop image updated successfully!',
                'file_path' => url('uploads/customers/adhaar_front_img/' . $filename), // Return full URL of uploaded file
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No image uploaded.'], 400);
    }

    
    public function updateCustomerAdharBackImage(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'adhaar_back_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('adhaar_back_img')) {
            // Retrieve the uploaded file
            $file = $request->file('adhaar_back_img');

            // Generate a unique filename
            $filename = time() . '_' . $file->getClientOriginalName();

            // Define the target directory path (inside public/uploads/customers/shop_image)
            $destinationPath = public_path('uploads/customers/adhaar_back_img');

            // Create the directory if it doesn't exist
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Move the uploaded file to the target directory
            $file->move($destinationPath, $filename);

            // Update user's shop image path in the database
            $user = User::findOrFail($request->user_id);

            if ($user->customerDetails && $user->customerDetails->adhaar_back_img) {
                // Delete the old image if it exists
                $oldImagePath = public_path($user->customerDetails->adhaar_back_img);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            // Save the new image path in the database
            $user->customerDetails->update(['adhaar_back_img' =>  $filename]);

            return response()->json([
                'success' => true,
                'message' => 'Shop image updated successfully!',
                'file_path' => url('uploads/customers/adhaar_back_img/' . $filename), // Return full URL of uploaded file
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No image uploaded.'], 400);
    }

    
    public function updateCustomerPanImage(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pan_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('pan_img')) {
            // Retrieve the uploaded file
            $file = $request->file('pan_img');

            // Generate a unique filename
            $filename = time() . '_' . $file->getClientOriginalName();

            // Define the target directory path (inside public/uploads/customers/shop_image)
            $destinationPath = public_path('uploads/customers/pan_img');

            // Create the directory if it doesn't exist
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Move the uploaded file to the target directory
            $file->move($destinationPath, $filename);

            // Update user's shop image path in the database
            $user = User::findOrFail($request->user_id);

            if ($user->customerDetails && $user->customerDetails->pan_img) {
                // Delete the old image if it exists
                $oldImagePath = public_path($user->customerDetails->pan_img);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            // Save the new image path in the database
            $user->customerDetails->update(['pan_img' =>  $filename]);

            return response()->json([
                'success' => true,
                'message' => 'Shop image updated successfully!',
                'file_path' => url('uploads/customers/pan_img/' . $filename), // Return full URL of uploaded file
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No image uploaded.'], 400);
    }
    
    public function updateCustomerBnkImage(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bank_cheque_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('bank_cheque_img')) {
            // Retrieve the uploaded file
            $file = $request->file('bank_cheque_img');

            // Generate a unique filename
            $filename = time() . '_' . $file->getClientOriginalName();

            // Define the target directory path (inside public/uploads/customers/shop_image)
            $destinationPath = public_path('uploads/customers/bank_cheque_img');

            // Create the directory if it doesn't exist
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Move the uploaded file to the target directory
            $file->move($destinationPath, $filename);

            // Update user's shop image path in the database
            $user = User::findOrFail($request->user_id);

            if ($user->customerDetails && $user->customerDetails->bank_cheque_img) {
                // Delete the old image if it exists
                $oldImagePath = public_path($user->customerDetails->bank_cheque_img);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            // Save the new image path in the database
            $user->customerDetails->update(['bank_cheque_img' =>  $filename]);

            return response()->json([
                'success' => true,
                'message' => 'Shop image updated successfully!',
                'file_path' => url('uploads/customers/bank_cheque_img/' . $filename), // Return full URL of uploaded file
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No image uploaded.'], 400);
    }
    
}