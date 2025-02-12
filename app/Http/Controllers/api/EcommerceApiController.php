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
    

    
    public function getAddresses(Request $request)
    {
        // Get authenticated user's ID from JWT token
        $user = auth()->user();

        // Fetch the default address ID from customer_details for the authenticated user
        $defaultAddressId = \App\Models\CustomerDetail::where('user_id', $user->id)->value('address_id');

        // Fetch all addresses belonging to the user
        $addresses = \App\Models\Address::where('user_id', $user->id)
            ->where('status', 1)
            ->get()
            ->map(function ($address) use ($defaultAddressId) {
                // Map address_type to human-readable format
                $address->address_type_label = $address->address_type == 0 ? 'Home' : 'Office';
                
                // Add default_address flag
                $address->default_address = ($address->id == $defaultAddressId);
                
                return $address;
            });

        return response()->json([
            'success' => true,
            'data' => $addresses,
        ], 200);
    }

    public function getAddresses(Request $request)
    {
        // Get authenticated user's ID from JWT token
        $user = auth()->user();
    
        // Fetch the default address ID from customer_details for the authenticated user
        $defaultAddressId = \App\Models\CustomerDetail::where('user_id', $user->id)->value('address_id');
    
        // Fetch all addresses belonging to the user
        $addresses = \App\Models\Address::where('user_id', $user->id)
            ->where('status', 1)
            ->get()
            ->map(function ($address) use ($defaultAddressId) {
                // Map address_type to human-readable format
                $address->address_type_label = $address->address_type == 0 ? 'Home' : 'Office';
                
                // Add default_address flag
                $address->default_address = ($address->id == $defaultAddressId);
                
                return $address;
            });
    
        return response()->json([
            'success' => true,
            'data' => $addresses,
        ], 200);
    }
    
    
    
        public function updateAddress(Request $request, $id)
        {
            // Get authenticated user's ID from JWT token
            $user = auth()->user();
    
            // Validate input data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'address1' => 'required|string|max:255',
                'address2' => 'nullable|string|max:255',
                'landmark' => 'nullable|string|max:255',
                'phone' => 'required|string|max:12',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'address_type' => 'required|integer|in:0,1', // 0: Home, 1: Office
                'pincode' => 'required|string|max:10',
            ]);
    
            // Find the address and check ownership
            $address = Address::where('id', $id)->where('user_id', $user->id)->first();
    
            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address not found or does not belong to the user.',
                ], 404);
            }
    
            // Update the address
            $address->update($validatedData);
    
            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully.',
                'data' => $address,
            ], 200);
        }
    
        public function setAddress(Request $request, $id)
        {
            // Get authenticated user's ID from JWT token
            $user = auth()->user();
    
            // Validate the request to ensure 'address_id' is provided
             $addressExists = \DB::table('addresses')->where('id', $id)->exists();
    
            if (!$addressExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address ID does not exist',
                ], 404);
            }
    
            // Find the customer's details associated with the authenticated user
            $customerDetails = CustomerDetails::where('user_id', $user->id)->first();
    
            if (!$customerDetails) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer details not found',
                ], 404);
            }
    
            // Update the address_id with the provided id
            $customerDetails->address_id = $id;
            $customerDetails->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully',
            ], 200);
        }
    
    
    
        public function deleteAddress(Request $request, $id)
        {
            // Get authenticated user's ID from JWT token
            $user = auth()->user();
    
            // Find the address and check ownership
            $address = Address::where('id', $id)->where('user_id', $user->id)->first();
    
            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address not found or does not belong to the user.',
                ], 404);
            }
    
            // Soft delete the address by setting deleted_at or status to inactive (if soft delete is enabled)
            $address->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Address deleted successfully.',
            ], 200);
        }

        
    public function orderCreate(Request $request)
    {
        // Retrieve user from JWT token
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized', 'status' => false], 401);
        }

        // Validate the request
        $request->validate([
            'address_id' => 'required|integer|exists:addresses,id',
        ]);

        // Find the user's cart
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found', 'status' => false], 404);
        }

        // Fetch cart items with product details
        $cartItems = CartItem::where('cart_id', $cart->id)
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->select(
                'products.id as product_id',
                'products.name',
                DB::raw("SUBSTRING_INDEX(products.image, ',', 1) as image"), // Get the first image
                'products.sale_price',
                'cart_items.quantity',
                DB::raw('products.sale_price * cart_items.quantity as total_price') // Calculate total price per item
            )
            ->get();

        // Calculate order totals
        $grandTotal = $cartItems->sum('total_price'); // Total price (grand total)
        $salePrice = $cartItems->sum(function ($item) {
            return $item->sale_price * $item->quantity; // Sale price per item
        });
        $totalTax = 0; // Assuming no tax calculation for now

        // Create a new order in the orders table
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $grandTotal,  // Grand total (matches order_total)
            'total_tax' => $totalTax,      // Total tax (0 for now)
            'sale_price' => $salePrice,   // Sale price (calculated from cart items)
            'sale_tax' => 0,              // Sale tax (0 for now)
            'order_amount' => count($cartItems), // Number of items in the order
            'order_status' => 0,          // Created status
            'payment_id' => null,
            'razor_order_id' => null,
            'address_id' => $request->address_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert items into order_items table
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'qty' => $item->quantity,
                'tax' => 0,                // Assuming no tax calculation for now
                'total_price' => $item->total_price, // Total price per item
                'sale_price' => $item->sale_price,   // Sale price per item
                'delivery_status' => 1,   // Default to delivered for now
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Soft delete all cart and cart items for the user
        CartItem::where('cart_id', $cart->id)->delete();
        Cart::where('id', $cart->id)->delete();

        // Return response with created order ID and status
        return response()->json([
            'message' => 'Order completed successfully.',
            'status' => true,
            'data' => [
                'order_id' => $order->id,
                'razor_order' => null, // Razorpay order ID is null for now
            ],
        ]);
    }

    public function userProfile(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();

        // Fetch associated CustomerDetails using the relationship
        $customerDetails = $user->customerDetails;

        // Return or process the data as needed
        return response()->json([
            'user' => $user
        ]);
    }

    public function uploadProfileImage(Request $request)
    {
        // Validate the request to ensure an image is provided
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048', // Max size: 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        // Retrieve the authenticated user from the JWT token
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        // Process the uploaded image
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Generate a unique file name with timestamp
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Define the upload path
            $uploadPath = public_path('upload/profiles');

            // Move the file to the upload path
            $file->move($uploadPath, $fileName);

            // Generate the file URL (relative path)
            $fileUrl = "/upload/profiles/$fileName";

            // Update the user's image column in the database
            $user->image = $fileUrl;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile image uploaded successfully.',
                'image_url' => $fileUrl,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No image file found in the request.',
        ], 400);
    } 
    public function search(Request $request)
	{
		$query = $request->input('query');

		if (!$query) {
			return response()->json(['error' => 'Query parameter is required'], 400);
		}

		// Check if 'catsearch' exists in the query
		if (str_contains($query, 'catsearch=')) {
			// Extract the value after 'catsearch='
			parse_str($query, $parsedQuery);
			$catsearchValue = $parsedQuery['catsearch'] ?? null;
			$catsearchValue = Str::lower(Str::squish($catsearchValue));
			
			if ($catsearchValue === 'all items') {
				// List all active products
				$products = Product::select('id', 'name', 'image', 'mrp', 'sale_price', 'bundle_qty')
					->get()
					->map(function ($product) {
						// Split the image field by commas and get the first image
						$images = explode(',', $product->image);
						$product->image = [$images[0] ?? null]; // Format as 'image[0]'
						return $product;
					});

				return response()->json($products);
			} else {
				// Normalize the category name by trimming spaces and converting to lowercase
				$normalizedCatsearchValue = strtolower(trim($catsearchValue));
			
				// Find the category ID using a case-insensitive comparison
				$category = Category::whereRaw('LOWER(TRIM(name)) = ?', [$normalizedCatsearchValue])->first();
				if (!$category) {
					return response()->json(['error' => 'Category not found'], 404);
				}

				// Get products with matching category_id
				$products = Product::where('cat_id', $category->id)
					->select('id', 'name', 'image', 'mrp', 'sale_price', 'bundle_qty')
					->get()
					->map(function ($product) {
						// Split the image field by commas and get the first image
						$images = explode(',', $product->image);
						$product->image = [$images[0] ?? null]; // Format as 'image[0]'
						return $product;
					});

				return response()->json($products);
			}
		}

		// Default behavior: Perform full-text search using MATCH...AGAINST
		$products = Product::whereRaw(
			"MATCH(name, description, specification) AGAINST(? IN NATURAL LANGUAGE MODE)",
			[$query]
		)->select('id', 'name', 'image', 'mrp', 'sale_price', 'bundle_qty')
		  ->get()
		  ->map(function ($product) {
			  // Split the image field by commas and get the first image
			  $images = explode(',', $product->image);
			  $product->image = [$images[0] ?? null]; // Format as 'image[0]'
			  return $product;
		  });

		return response()->json($products);
	}

    
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        // Find or create a cart for the user
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Check if the product is already in the cart
        $cartItem = CartItem::where('cart_id', $cart->id)
                            ->where('product_id', $request->product_id)
                            ->first();

        if ($cartItem) {
            // Increment quantity if it exists
            $cartItem->increment('quantity', $request->quantity);
        } else {
            // Add new item to the cart
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json(['message' => 'Product added to cart successfully']);
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        
        // Find user's cart
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        // Find the cart item
        $cartItem = CartItem::where('cart_id', $cart->id)
                            ->where('product_id', $request->product_id)
                            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Product not found in cart'], 404);
        }

        // Decrease quantity or remove item
        if ($cartItem->quantity > $request->quantity) {
            $cartItem->decrement('quantity', $request->quantity);
            return response()->json(['message' => 'Product quantity decreased']);
        } else {
            $cartItem->delete();
            return response()->json(['message' => 'Product removed from cart']);
        }
    }


    public function deleteCartItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = $request->user();
        
        // Find user's cart
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        // Delete the specific item from the cart
        CartItem::where('cart_id', $cart->id)
                ->where('product_id', $request->product_id)
                ->delete();

        return response()->json(['message' => 'Product removed from cart']);
    }

    public function clearCart(Request $request)
    {
        $user = $request->user();
        
        // Find user's cart
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        // Delete all items in the cart
        CartItem::where('cart_id', $cart->id)->delete();

        return response()->json(['message' => 'Cart cleared successfully']);
    }

    public function getCartItems(Request $request)
    {
        // Get the authenticated user from the token
        $user = $request->user();
    
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        // Find the user's cart
        $cart = Cart::where('user_id', $user->id)->first();
    
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
    
        // Fetch cart items with product details
        $cartItems = CartItem::where('cart_id', $cart->id)
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->select(
                'cart_items.id as cart_item_id',
                'products.id as product_id',
                'products.name',
                DB::raw("SUBSTRING_INDEX(products.image, ',', 1) as image"), // Get the first image
                'products.sale_price',
                'products.mrp',
                'cart_items.quantity'
            )
            ->get();
    
        return response()->json([
            'success' => true,
            'message' => 'Cart items fetched successfully.',
            'data' => $cartItems,
        ]);
    }
    
    public function checkout(Request $request)
    {
        // Get the authenticated user from the token
        $user = $request->user();
    
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        // Find the user's cart
        $cart = Cart::where('user_id', $user->id)->first();
    
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
    
        // Fetch cart items with product details
        $cartItems = CartItem::where('cart_id', $cart->id)
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->select(
                'products.id as product_id',
                'products.name',
                DB::raw("SUBSTRING_INDEX(products.image, ',', 1) as image"), // Get the first image
                'products.sale_price',
                'cart_items.quantity',
                DB::raw('products.sale_price * cart_items.quantity as total_price') // Calculate total price per item
            )
            ->get();
    
        // Calculate grand total
        $grandTotal = $cartItems->sum('total_price');
    
        // Fetch customer details
        $customerDetails = CustomerDetails::where('user_id', $user->id)->first();
        $gstNumber = $customerDetails && $customerDetails->gst_number ? $customerDetails->gst_number : "";
    
        // Fetch address details if address_id is set
        $address = null;
        $isActiveAddress = 0;

       
    
        if ($customerDetails && $customerDetails->address_id) {
            $address = Address::where('id', $customerDetails->address_id)
                ->where('user_id', $user->id)
                ->first();
            if ($address) {
                $isActiveAddress = 1;
            }
        }else{
            $address = Address::where('user_id', $user->id)
            ->where('user_id', $user->id)
            ->first();
            if ($address) {
                $isActiveAddress = 1;
            }
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Checkout details fetched successfully.',
            'data' => [
                'items' => $cartItems,
                'grand_total' => $grandTotal,
                'customer_details' => [
                    'gst_number' => $gstNumber,
                ],
                'address' => $address,
                'is_active_address' => $isActiveAddress,
            ],
        ]);
    }
}
