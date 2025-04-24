<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use App\Models\Product;
use App\Models\Pricing;
use App\Models\Slider;
use App\Models\Service;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReason;
use App\Models\Otp;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Rating;
use App\Models\Variant;
use App\Models\SellerPrice;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyOtpMail;


class EcommerceApiController extends Controller
{
    public function createAddress(Request $request)
    {
        // Validate the incoming request data;
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
        $user = auth()->user(); // Get the authenticated user
    
        // Create a new address entry
        $address = Address::create([
            'user_id' => $user->id,
            'name' => $validatedData['name'],
            'address1' => $validatedData['address1'],
            'address2' => $validatedData['address2'] ?? null,
            'landmark' => $validatedData['landmark'] ?? null,
            'phone' => $validatedData['phone'],
            'city' => $validatedData['city'],
            'state' => $validatedData['state'],
            'address_type' => $validatedData['address_type'], // 0 for Home, 1 for Office
            'pincode' => $validatedData['pincode'],
            'status' => 1, // Active status by default
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Address created successfully.',
            'data' => [
                "id" => $address->id,
                "user_id" => $address->user_id,
                "name" => $address->name,
                "address1" => $address->address1,
                "address2" => $address->address2,
                "landmark" => $address->landmark,
                "phone" => $address->phone,
                "city" => $address->city,
                "state" => $address->state,
                "pincode" => $address->pincode,
                "status" => (bool)$address->status, // Convert status to boolean
                "address_type_label" =>
                    ($address->address_type == 0) ? "Home" : "Office", // Add label for address type
            ],
        ], 201);
    }
    
    

    
    public function getAddresses(Request $request)
    {
        // Get authenticated user's ID from JWT token
        $user = auth()->user();

        // Fetch the default address ID from customer_details for the authenticated user
        $defaultAddressId = $user->default_address;

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
        // Get authenticated user
        $user = auth()->user();
    
        // Validate input data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'phone' => 'required|string|max:15', // Increased max length to 15 for international numbers
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
    
        try {
            // Update the address
            $address->update([
                'name' => $validatedData['name'],
                'address1' => $validatedData['address1'],
                'address2' => $validatedData['address2'] ?? null,
                'landmark' => $validatedData['landmark'] ?? null,
                'phone' => $validatedData['phone'],
                'city' => $validatedData['city'],
                'state' => $validatedData['state'],
                'address_type' => $validatedData['address_type'], // 0 for Home, 1 for Office
                'pincode' => $validatedData['pincode'],
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully.',
                'data' => [
                    "id" => $address->id,
                    "user_id" => $address->user_id,
                    "name" => $address->name,
                    "address1" => $address->address1,
                    "address2" => $address->address2,
                    "landmark" => $address->landmark,
                    "phone" => $address->phone,
                    "city" => $address->city,
                    "state" => $address->state,
                    "pincode" => $address->pincode,
                    "status" => (bool)$address->status, // Convert status to boolean
                    "address_type_label" =>
                        ($address->address_type == 0) ? "Home" : "Office", // Add label for address type
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Failed to update address: {$e->getMessage()}",
            ], 500);
        }
    }
    
    
    public function setAddress(Request $request, $id)
    {
        // Get authenticated user's ID from JWT token
        $user = auth()->user();
    
        // Check if the address exists and retrieve its pincode
        $address = \DB::table('addresses')->where('id', $id)->first();
    
        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address ID does not exist',
            ], 404);
        }
    
        // Find the customer's details associated with the authenticated user
        $customerDetails = User::where('id', $user->id)->first();
    
        if (!$customerDetails) {
            return response()->json([
                'success' => false,
                'message' => 'Customer details not found',
            ], 404);
        }
    
        // Update the default_address and pincode with the retrieved values
        $customerDetails->default_address = $id;
        $customerDetails->pincode = $address->pincode; // Set pincode from address record
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
                'products.mrp',
                'cart_items.quantity',
                DB::raw('products.mrp * cart_items.quantity as total_price') // Calculate total price per item
            )
            ->get();

        // Calculate order totals
        $grandTotal = $cartItems->sum('total_price'); // Total price (grand total)
        $salePrice = $cartItems->sum(function ($item) {
            return $item->mrp * $item->quantity; // Sale price per item
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
                'sale_price' => $item->mrp,   // Sale price per item
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
        'photo' => 'required',
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
    $photoData = $request->input('photo');
    if (isset($photoData['_parts'])) {
        $fileData = $photoData['_parts'][0][1];
        $fileUri = $fileData['uri'];
        $fileName = $fileData['name'];
        $fileType = $fileData['type'];

        // Check if the file is an image
        if (!in_array($fileType, ['image/jpeg', 'image/jpg', 'image/png'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only JPEG, JPG, and PNG images are allowed.',
            ], 400);
        }

        // Check the file size
        $fileSize = filesize($fileUri);
        if ($fileSize > 2048 * 1024) { // Max size: 2MB
            return response()->json([
                'success' => false,
                'message' => 'Image size exceeds the maximum allowed size of 2MB.',
            ], 400);
        }

        // Generate a unique file name with timestamp
        $newFileName = time() . '_' . uniqid() . '.' . pathinfo($fileName, PATHINFO_EXTENSION);

        // Define the upload path
        $uploadPath = public_path('upload/profiles');

        // Move the file to the upload path
        // Since the file is not directly accessible via Laravel's file handling,
        // you might need to use PHP's built-in functions to copy the file.
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        copy($fileUri, $uploadPath . '/' . $newFileName);

        // Generate the file URL (relative path)
        $fileUrl = "/upload/profiles/$newFileName";

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
        $limit = $request->input('limit', 10); // Default limit is 10
        $sort = $request->input('sort', 'asc'); // Default sorting is ascending (alphabetical)
    
        if (!$query) {
            return response()->json(['error' => 'Query parameter is required'], 400);
        }
    
        // Normalize the query for case-insensitive and trimmed comparison
        $normalizedQuery = Str::lower(Str::squish($query));
    
        // Validate sorting parameter
        if (!in_array($sort, ['asc', 'desc'])) {
            return response()->json(['error' => 'Invalid sort parameter. Use "asc" or "desc".'], 400);
        }
    
        // Check if 'catsearch' exists in the query
        if (str_contains($normalizedQuery, 'catsearch=')) {
            // Extract the value after 'catsearch='
            parse_str($query, $parsedQuery);
            $catsearchValue = $parsedQuery['catsearch'] ?? null;
            $catsearchValue = Str::lower(Str::squish($catsearchValue));
    
            if ($catsearchValue === 'all items') {
                // List all active products
                $products = Product::where('status', 1)
                    ->select('id', 'name', 'image')
                    ->orderBy('name', $sort) // Sort alphabetically by name
                    ->limit($limit) // Apply limit
                    ->get()
                    ->map(function ($product) {
                        // Split the image field by commas and get the first image
                        $images = explode(',', $product->image);
                        $product->image = $images[0] ?? null; // Get only the first image
                        return $product;
                    });
    
                return response()->json([
                    'isSuccess' => true,
                    'errors' => [
                        'message' => 'Products retrieved successfully.',
                    ],
                    'data' => $products,
                ]);
            } else {
                // Find category by name
                $category = Category::whereRaw('LOWER(TRIM(name)) = ?', [$catsearchValue])->first();
    
                if (!$category) {
                    return response()->json(['error' => 'Category not found'], 404);
                }
    
                // Get products with matching category_id
                $products = Product::where('cat_id', $category->id)
                    ->where('status', 1)
                    ->select('id', 'name', 'image')
                    ->orderBy('name', $sort) // Sort alphabetically by name
                    ->limit($limit) // Apply limit
                    ->get()
                    ->map(function ($product) {
                        // Split the image field by commas and get the first image
                        $images = explode(',', $product->image);
                        $product->image = $images[0] ?? null; // Get only the first image
                        return $product;
                    });
    
                return response()->json([
                    'isSuccess' => true,
                    'errors' => [
                        'message' => 'Products retrieved successfully.',
                    ],
                    'data' => $products,
                ]);
            }
        }
    
        // Default behavior: Search across all varchar fields in products and variants
    
        // Search in products table
        $productsFromProductsTable = Product::where(function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
            ->orWhere('availability', 'LIKE', "%{$query}%");
        })
            ->where('status', 1)
            ->select('id', 'name', 'image')
            ->orderBy('name', $sort) // Sort alphabetically by name
            ->limit($limit) // Apply limit
            ->get()
            ->map(function ($product) {
                // Split the image field by commas and get the first image
                $images = explode(',', $product->image);
                $product->image = $images[0] ?? null; // Get only the first image
                return $product;
            });
    
        // Search in variants table and join with products table
        $productsFromVariantsTable = Product::join('variants', 'products.id', '=', 'variants.product_id')
            ->where(function ($q) use ($query) {
                $q->where('variants.name', 'LIKE', "%{$query}%")
                ->orWhere('variants.sku', 'LIKE', "%{$query}%")
                ->orWhere('variants.short_description', 'LIKE', "%{$query}%");
            })
            ->where('products.status', 1)
            ->select('products.id', 'products.name', 'products.image')
            ->distinct()
            ->orderBy('products.name', $sort) // Sort alphabetically by name
            ->limit($limit) // Apply limit
            ->get()
            ->map(function ($product) {
                // Split the image field by commas and get the first image
                $images = explode(',', $product->image);
                $product->image = $images[0] ?? null; // Get only the first image
                return $product;
            });
    
        // Merge results from both tables and remove duplicates by ID
        $mergedProducts = collect($productsFromProductsTable)
            ->merge($productsFromVariantsTable)
            ->unique('id')
            ->values();
    
        if ($mergedProducts->isEmpty()) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'No products found.',
                ],
                'data' => [],
            ], 404);
        }
    
        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Products retrieved successfully.',
            ],
            'data' => $mergedProducts,
        ]);
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
    
    
    public function listProductsBySubCategory(Request $request)
    {
        // Retrieve 'sub_cat_id', 'limit', and 'sort' query parameters
        $subCatId = $request->query('sub_cat_id');
        $limit = $request->query('limit', 10); // Default limit is 10 if not provided
        $sort = $request->query('sort', 'asc'); // Default sorting is ascending

        // Validate the 'sort' parameter
        if (!in_array($sort, ['asc', 'desc'])) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Invalid sort parameter. Only "asc" or "desc" are allowed.',
                ],
                'data' => [],
            ], 400);
        }

        // Validate that sub_cat_id is provided
        if (!$subCatId) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'The sub_cat_id parameter is required.',
                ],
                'data' => [],
            ], 400);
        }

        // Fetch products by sub category ID with sorting and limiting
        $products = Product::where('sub_cat_id', $subCatId)
            ->orderBy('id', $sort) // Sort by 'id'; change as needed
            ->limit($limit)
            ->get(['id', 'name', 'image']); // Select only id, name, and image

        // Process the products to extract the first image from the comma-separated list
        $products = $products->map(function ($product) {
            $images = explode(',', $product->image); // Split the image string by commas
            $product->image = $images[0] ?? null; // Take the first image or set null if empty
            return $product;
        });

        // Check if products exist
        if ($products->isEmpty()) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'No products found for the given sub category.',
                ],
                'data' => [],
            ], 404);
        }

        // Return success response with products
        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Products retrieved successfully.',
            ],
            'data' => $products,
        ], 200);
    }

    public function listSliders()
    {
        // Fetch sliders where status = 1
        $sliders = Slider::where('status', 1)
            ->orderBy('pos', 'asc') // Order by position (pos) in ascending order
            ->get(['id', 'image', 'pos']); // Select only id, image, and pos fields

        // Check if sliders exist
        if ($sliders->isEmpty()) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'No sliders found.',
                ],
                'data' => [],
            ], 404);
        }

        // Return success response with sliders
        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Sliders retrieved successfully.',
            ],
            'data' => $sliders,
        ], 200);
    }


    public function getProductDetail(Request $request, $productId)
    {
        // Fetch the product by ID
        $product = Product::with('category', 'subcategory', 'variants')->find($productId);

        if (!$product) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Product not found.',
                ],
                'data' => null,
            ], 404);
        }

          // Convert the image field to an array
        $images = $product->image ? explode(',', $product->image) : [];
        // Get the authenticated user's default address
        $user = $request->user(); // Assuming user is authenticated
        $defaultPincode = null;

        if ($user && $user->default_address) {
            $address = Address::find($user->default_address);
            if ($address) {
                $defaultPincode = $address->pincode;
                 // Update the user's pincode
				$user->pincode = $defaultPincode;
				$user->save(); // Save the updated user record to the database
            }
        }else{
			 $address = Address::select(['id','pincode'])->where('user_id', $user->id)->first();
			if ($address) {
                $defaultPincode = $address->pincode;
                 // Update the user's pincode
				$user->pincode = $defaultPincode;
				$user->save(); // Save the updated user record to the database
            }
		}

        // Prepare variants with pricing
        $variants = $product->variants->map(function ($variant) use ($defaultPincode, $product) {
            // Fetch pricing based on pincode
            $pricing = null;
            if ($defaultPincode) {
                $pricing = Pricing::where('pincode', $defaultPincode)
                    ->where('product_id', $product->id)
                    ->where('product_sku_id', $variant->id)
                    ->where('status', 1)
                    ->first();
            }

             // Map the type field to human-readable values
            $typeMapping = [
                1 => 'Quality',
                2 => 'Colour',
                3 => 'Size',
            ];
            $typeName = $typeMapping[$variant->type] ?? 'Unknown'; // Default to "Unknown" if type is not mapped

            return [
                'id' => $variant->id,
                'name' => $variant->name,
                'sku' => $variant->sku,
                'short_description' => $variant->short_description,
                'min_quantity' => $variant->min_quantity,
                'type' => $typeName, // Add the mapped type name
                'price' => $pricing ? [
                    'mrp' => $pricing->mrp,
                    'price' => $pricing->price,
                    'tax_type' => $pricing->tax_type,
                    'tax_value' => $pricing->tax_value,
                    'ship_charges' => $pricing->ship_charges,
                    'valid_upto' => $pricing->valid_upto,
                    'is_cash' => $pricing->is_cash,
                ] : null, // If no pricing is found, return null
            ];
        });

        // Prepare the response
        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Product details retrieved successfully.',
            ],
            'data' => [
                ...$product->toArray(),
                'image' => $images,
                'category' => [
                    'id' => $product->category?->id, // Use optional chaining to avoid errors if category is null
                    'name' => $product->category?->name,
                ],
                'subcategory' => [
                    'id' => $product->subcategory?->id, // Use optional chaining to avoid errors if subcategory is null
                    'name' => $product->subcategory?->name,
                ],
                'variants' => $variants,
            ],
        ], 200);
    }


    public function addRating(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'variant_id' => 'required|exists:variants,id',
            'rating' => 'required|integer|min:1|max:5', // Rating between 1 and 5
            'review' => 'nullable|string|max:1000',
        ]);

        $user = $request->user(); // Get the authenticated user

        // Check if the user has purchased this variant before
        $hasPurchased = OrderItem::whereHas('order', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('variant_id', $validated['variant_id'])->exists();

        if (!$hasPurchased) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'You can only review products you have purchased.',
                ],
            ], 403);
        }

        // Check if the user has already reviewed this variant
        $existingRating = Rating::where('user_id', $user->id)
            ->where('variant_id', $validated['variant_id'])
            ->first();

        if ($existingRating) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'You have already reviewed this product.',
                ],
            ], 400);
        }

        // Create the rating and review
        $rating = Rating::create([
            'user_id' => $user->id,
            'variant_id' => $validated['variant_id'],
            'rating' => $validated['rating'],
            'review' => $validated['review'],
        ]);

        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Thank you for your feedback!',
            ],
            'data' => $rating,
        ], 201);
    }


    public function updateRating(Request $request, $ratingId)
    {
        // Validate the request data
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5', // Rating between 1 and 5
            'review' => 'nullable|string|max:1000',
        ]);

        // Find the rating by ID
        $rating = Rating::find($ratingId);

        if (!$rating) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Rating not found.',
                ],
            ], 404);
        }

        // Check if the authenticated user owns the rating
        if ($request->user()->id !== $rating->user_id) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Unauthorized to update this rating.',
                ],
            ], 403);
        }

        // Update the rating and review
        $rating->update([
            'rating' => $validated['rating'],
            'review' => $validated['review'],
        ]);

        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Rating updated successfully.',
            ],
            'data' => $rating,
        ], 200);
    }

    public function deleteRating(Request $request, $ratingId)
    {
        // Find the rating by ID
        $rating = Rating::find($ratingId);

        if (!$rating) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Rating not found.',
                ],
            ], 404);
        }

        // Check if the authenticated user owns the rating
        if ($request->user()->id !== $rating->user_id) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Unauthorized to delete this rating.',
                ],
            ], 403);
        }

        // Delete the rating
        $rating->delete();

        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Rating deleted successfully.',
            ],
            'data' => null,
        ], 200);
    }


    public function getVariantReviews(Request $request, $variantId)
    {
        // Get all reviews for the variant
        $reviews = Rating::where('variant_id', $variantId)->get();

        if ($reviews->isEmpty()) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'No reviews found for this variant.',
                ],
                'data' => [],
            ], 404);
        }

        // Calculate average rating
        $averageRating = round($reviews->avg('rating'), 2); // Rounded to 2 decimal places

        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Reviews retrieved successfully.',
            ],
            'data' => [
                'average_rating_out_of_5' => $averageRating,
                'reviews_count' => $reviews->count(),
                'reviews' => $reviews,
            ],
        ], 200);
    }


    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id', // Validate product ID
            'variant_id' => 'required|exists:variants,id', // Validate variant ID
            'quantity' => 'required|integer|min:1',
        ]);
    
        $user = $request->user();
    
        // Get or create a cart for the user
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
    
        // Check if the item is already in the cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('variant_id', $validated['variant_id'])
            ->first();
    
        if ($cartItem) {
            // Update quantity if item exists
            $cartItem->update(['quantity' => $cartItem->quantity + $validated['quantity']]);
        } else {
            // Add new item to the cart
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $validated['product_id'], // Include product ID in the cart item
                'variant_id' => $validated['variant_id'],
                'quantity' => $validated['quantity'],
            ]);
        }
    
        // Count the total number of distinct variants in the cart
        $itemCount = CartItem::where('cart_id', $cart->id)->count();
    
        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Item added to cart successfully.',
            ],
            'data' => [
                'item_count' => $itemCount, // Include count of items in the cart
            ],
        ], 201);
    }
    
    public function removeFromCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:variants,id',
            'quantity' => 'required|integer|min:1',
        ]);
    
        $user = $request->user();
    
        // Get or create a cart for the user
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
    
        // Check if the item is already in the cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('variant_id', $validated['variant_id'])
            ->first();
    
        if ($cartItem) {
            // Decrease the quantity by requested amount
            $newQuantity = $cartItem->quantity - $validated['quantity'];
    
            if ($newQuantity <= 0) {
                // Soft delete if quantity is zero or negative
                $cartItem->delete(); // Ensure CartItem model uses SoftDeletes trait
            } else {
                // Update quantity if still positive
                $cartItem->update(['quantity' => $newQuantity]);
            }
        } else {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Item not found in cart.',
                ],
                'data' => [],
            ], 404);
        }
    
        // Count the total number of distinct variants in the cart (excluding soft-deleted items)
        $itemCount = CartItem::where('cart_id', $cart->id)->count();
    
        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Item removed from cart successfully.',
            ],
            'data' => [
                'item_count' => $itemCount,
            ],
        ], 200);
    }
    
    


    public function deleteFromCart(Request $request, $itemId)
    {
        // Get the authenticated user
        $user = Auth::user();
    
        // Find the cart item by variant_id, ensuring it belongs to the authenticated user
        $cartItem = CartItem::whereHas('cart', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('variant_id', $itemId)->first();
    
        if (!$cartItem) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Cart item not found.',
                ],
            ], 404);
        }
    
        // Perform soft delete on the cart item
        $cartItem->delete();
    
        // Count remaining distinct variants in the user's cart after deletion
        $itemCount = CartItem::where('cart_id', $cartItem->cart_id)->count();
    
        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Item removed from cart successfully.',
            ],
            'data' => [
                'item_count' => $itemCount,
            ],
        ], 200);
    }
    
    


    public function clearCart(Request $request)
    {
        $user = $request->user();

        // Get the user's cart and delete all items in it
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'No active cart found.',
                ],
            ], 404);
        }

        // Delete all items in the cart
        CartItem::where('cart_id', $cart->id)->delete();

        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Cart cleared successfully.',
            ],
            'data' => null,
        ], 200);
    }


    


    public function calculateCheckout(Request $request)
    {
        $user = $request->user(); // Get the authenticated user
    
        // Step 1: Get or create a single cart for the user
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
    
        // Step 2: Get the user's default address
        if (!$user->default_address) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Default address not found for the user.',
                ],
            ], 404);
        }
    
        $address = Address::find($user->default_address);
        if (!$address) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Address not found.',
                ],
            ], 404);
        }
    
        $pincode = $address->pincode;

        if(empty($pincode)){
            $address = Address::select(['id','pincode'])->where('user_id', $user->id)->first();
           if ($address) {
               $pincode = $address->pincode;
           }
       }
    
        // Step 3: Get the user's cart items
        $cartItems = CartItem::with('variant')->where('cart_id', $cart->id)->get();
        if (!$cartItems->count()) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Your cart is empty.',
                ],
            ], 400);
        }
    
        // Initialize totals
        $totalQuantity = 0;
        $totalMrp = 0;
        $totalPrice = 0;
        $totalTaxes = 0;
        $totalShipping = 0;
    
        // Prepare product pricing details
        $productPricingDetails = [];
       
        foreach ($cartItems as $item) {
            // Fetch pricing for each variant based on pincode and SKU
            $pricing = Pricing::where('pincode', $pincode)
                ->where('product_sku_id', $item->variant->sku)
                ->where('status', 1)
                ->first();
            if (!$pricing) {
                return response()->json([
                    'isSuccess' => false,
                    'errors' => [
                        'message' => "Pricing not found for variant SKU {$item->variant->sku} with pincode {$pincode}.",
                    ],
                ], 404);
            }
    
            // Calculate tax based on tax type (percentage or flat)
            $tax = ($pricing->tax_type == 0) ? ($pricing->price * ($pricing->tax_value / 100)) : $pricing->tax_value;
    
            // Add to totals
            $totalQuantity += $item->quantity;
            $totalMrp += ($pricing->mrp * $item->quantity);
            $totalPrice += ($pricing->price * $item->quantity);
            $totalTaxes += ($tax * $item->quantity);
            $totalShipping += ($pricing->ship_charges * $item->quantity);
            //dd($item->variant?->product->image);
            // Add product details to the response object
            $productPricingDetails[] = [
                'variant_id' => $item->variant_id,
                'product_id' => optional($item->variant)->product_id,
                'name' => optional($item->variant)->name, // Variant name
                'sku' => optional($item->variant)->sku,
                'quantity' => $item->quantity,
                'mrp' => round($pricing->mrp, 2),
                'price' => round($pricing->price, 2),
                'tax_type' => ($pricing->tax_type == 0) ? 'percentage' : 'flat',
                'tax_value' => round($pricing->tax_value, 2),
                'shipping_charges' => round($pricing->ship_charges, 2),
                'subtotal_mrp' => round(($pricing->mrp * $item->quantity), 2),
                'subtotal_price' => round(($pricing->price * $item->quantity), 2),
                'subtotal_tax' => round(($tax * $item->quantity), 2),
                'subtotal_shipping' => round(($pricing->ship_charges * $item->quantity), 2),
                // Include product and variant images
                'product_name' => optional($item->variant?->product)->name, // Product name
                'video' => explode(",",optional($item->variant?->product)->video) ?? [], // Product image URL
                'product_image_url' => explode(",",optional($item->variant?->product)->image) ?? [], // Product image URL
                'variant_image_url' => explode(",",optional($item?->variant)->images) ?? [], // Variant image URL
            ];
        }
    

        
        $discount = 0;
        $appliedCouponObject = null;
    
        if ($cart->coupon_id) {
            // Fetch coupon details
            $coupon = Coupon::find($cart->coupon_id);
    
            if ($coupon && now()->lessThanOrEqualTo($coupon->expiry_date) && $coupon->status == 1) {
                // Check if cart meets minimum value for coupon
                if ($totalPrice >= $coupon->min_cart_value) {
                    // Calculate discount based on coupon type
                    if ($coupon->type == 'flat') {
                        $discount = min($coupon->value, ($coupon->max_discount_value ?? PHP_INT_MAX));
                    } elseif ($coupon->type == 'percentage') {
                        $discount = min(($totalPrice * ($coupon->value / 100)), ($coupon->max_discount_value ?? PHP_INT_MAX));
                    }
    
                    // Prepare applied coupon object for response
                    $appliedCouponObject = [
                        "id" => $coupon->id,
                        "name" => $coupon->name,
                        "type" => ucfirst($coupon->type),
                        "value" => round($coupon->value, 2),
                        "max_discount_value" => round($coupon->max_discount_value ?? 0, 2),
                        "min_cart_value" => round($coupon->min_cart_value, 2),
                        "expiry_date" => (string)$coupon->expiry_date,
                    ];
                } else {
                    // Remove invalid coupon from cart
                    $cart->coupon_id = null;
                    $cart->save();
                    return response()->json([
                        "isSuccess" => false,
                        "errors" => [
                            "message" => "The applied coupon does not meet the minimum cart value requirements.",
                        ],
                    ], 400);
                }
            }
        }

        $grandTotal = max(($totalPrice + $totalShipping - $discount), 0);
    
        // Step 4: Update Cart Totals in Database (manually save values)
        try {
            // Manually assign values and save
            $cart->total_quantity = round($totalQuantity);
            $cart->total_mrp = round($totalMrp, 2);
            $cart->total_price = round($totalPrice, 2);
            $cart->total_taxes = round($totalTaxes, 2);
            $cart->total_shipping_charges = round($totalShipping, 2);
            $cart->discount = round($discount, 2);
            $cart->grand_total = round($grandTotal, 2);
            
            // Save cart updates
            $cart->save();
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => "Failed to update cart: {$e->getMessage()}",
                ],
            ], 500);
        }

    
        return response()->json([
            "isSuccess" => true,
            "errors" => null,
            "data" => [
                "products_pricing_details" => $productPricingDetails,
                "cart_totals" => [
                    "total_quantity" => round($totalQuantity),
                    "total_mrp" => round($totalMrp, 2),
                    "total_price" => round($totalPrice, 2),
                    "total_taxes" => round($totalTaxes, 2),
                    "total_shipping_charges" => round($totalShipping, 2),
                    "discount" => round($discount, 2),
                    "coupon" => (!empty($coupon))?$coupon:[],
                    "grand_total" => round($grandTotal, 2)
                ],
                "active_address" => [
                    "id" => $address->id,
                    "name" => $address->name,
                    "phone" => $address->phone,
                    "address_line_1" => $address->address1,
                    "address_line_2" => isset($address->address2) ? trim($address->address2) : '',
                    "landmark" => isset($address->landmark) ? trim($address->landmark) : '',
                    "city" => trim($address->city),
                    "state" => trim($address->state),
                    "country" => isset($address->country) ? trim($address->country) : '',
                    "zip_code" => trim($address->pincode),
                ]
            ]
        ]);
    }
    
    
    

public function listAvailableCoupons()
{
    // Fetch all active and non-expired coupons
    $coupons = Coupon::where('status', 1) // Active coupons only
        ->where('expiry_date', '>=', now()) // Coupons that have not expired
        ->get();

    if ($coupons->isEmpty()) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => 'No available coupons found.',
            ],
            'data' => [],
        ], 404);
    }

    return response()->json([
        'isSuccess' => true,
        'errors' => null,
        'data' => $coupons,
    ], 200);
}

public function applyCoupon(Request $request)
{
    $validated = $request->validate([
        'coupon_code' => 'required|string', // Validate coupon code
    ]);

    $user = $request->user(); // Get the authenticated user

    // Fetch the user's cart (ensure one cart per user)
    $cart = Cart::where('user_id', $user->id)->first();
    if (!$cart) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => 'No active cart found for the user.',
            ],
        ], 404);
    }

    // Fetch the coupon details
    $coupon = Coupon::where('name', $validated['coupon_code'])
        ->where('status', 1) // Active coupon
        ->where('expiry_date', '>=', now()) // Non-expired coupon
        ->first();

    if (!$coupon) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => "Invalid or expired coupon code.",
            ],
        ], 400);
    }

    // Check if the cart meets the minimum value for this coupon (based on grand_total)
    if ($cart->grand_total < $coupon->min_cart_value) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => "The minimum cart value for this coupon is {$coupon->min_cart_value}.",
            ],
        ], 400);
    }

    // Calculate discount based on coupon type
    $discount = 0;
    if ($coupon->type == 'flat') {
        $discount = min($coupon->value, ($coupon->max_discount_value ?? PHP_INT_MAX));
    } elseif ($coupon->type == 'percentage') {
        $discount = min(($cart->grand_total * ($coupon->value / 100)), ($coupon->max_discount_value ?? PHP_INT_MAX));
    }

    // Calculate new grand total after applying discount
    $grandTotalAfterDiscount = max(($cart->grand_total - $discount), 0);

    // Update the cart with the applied coupon and discount (manually save values)
    try {
        $cart->coupon_id = $coupon->id; // Save applied coupon ID
        $cart->discount = round($discount, 2); // Save calculated discount
        $cart->grand_total = round($grandTotalAfterDiscount, 2); // Save updated grand total
        $cart->save(); // Save changes to the database
    } catch (\Exception $e) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => "Failed to apply coupon: {$e->getMessage()}",
            ],
        ], 500);
    }

    return response()->json([
        'isSuccess' => true,
        'errors' => null,
        'data' => [
            'message' => "Coupon applied successfully.",
            'cart_totals' => [
                "total_quantity" => round($cart->total_quantity),
                "total_mrp" => round($cart->total_mrp, 2),
                "total_price" => round($cart->total_price, 2),
                "total_taxes" => round($cart->total_taxes, 2),
                "total_shipping_charges" => round($cart->total_shipping_charges, 2),
                "discount" => round($discount, 2),
                "grand_total" => round($grandTotalAfterDiscount, 2),
            ],
            'applied_coupon' => [
                "id" => $coupon->id,
                "name" => $coupon->name,
                "type" => $coupon->type,
                "value" => round($coupon->value, 2),
                "max_discount_value" => round($coupon->max_discount_value ?? 0, 2),
                "min_cart_value" => round($coupon->min_cart_value, 2),
                "expiry_date" => $coupon->expiry_date,
            ],
        ],
    ], 200);
}


public function removeCoupon(Request $request)
{
    $user = $request->user(); // Get the authenticated user

    // Fetch the user's cart (ensure one cart per user)
    $cart = Cart::where('user_id', $user->id)->first();
    if (!$cart) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => 'No active cart found for the user.',
            ],
        ], 404);
    }

    // Check if a coupon is currently applied
    if (!$cart->coupon_id) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => 'No coupon is currently applied to the cart.',
            ],
        ], 400);
    }

    try {
        // Remove the coupon and reset discount
        $cart->coupon_id = null; // Remove applied coupon ID
        $cart->discount = 0; // Reset discount to 0
        $cart->grand_total = round($cart->total_price + $cart->total_shipping_charges, 2); // Recalculate grand total without discount
        $cart->save(); // Save changes to the database
    } catch (\Exception $e) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => "Failed to remove coupon: {$e->getMessage()}",
            ],
        ], 500);
    }

    return response()->json([
        'isSuccess' => true,
        'errors' => null,
        'data' => [
            'message' => "Coupon removed successfully.",
            'cart_totals' => [
                "total_quantity" => round($cart->total_quantity),
                "total_mrp" => round($cart->total_mrp, 2),
                "total_price" => round($cart->total_price, 2),
                "total_taxes" => round($cart->total_taxes, 2),
                "total_shipping_charges" => round($cart->total_shipping_charges, 2),
                "discount" => 0.00, // Discount is now 0
                "grand_total" => round($cart->grand_total, 2),
            ],
        ],
    ], 200);
}



public function createOrder(Request $request)
{
    $user = $request->user();
   
    // Fetch the user's cart with items and variants
    $cart = Cart::with(['items.variant', 'coupon'])->where('user_id', $user->id)->first();

    // Validation checks
    if (!$cart || !$cart->items->count()) {
        return response()->json(['error' => 'Cart is empty'], 400);
    }

    // Address validation
    if (!$user->default_address) {
        return response()->json(['error' => 'Default address not set'], 400);
    }
    
    $address = Address::find($request->address_id);
    if (!$address) {
        return response()->json(['error' => 'Address not found'], 404);
    }

    // Initialize Razorpay API
    $apiKey = env('RAZORPAY_KEY');
    $apiSecret = env('RAZORPAY_SECRET');
    $razorpay = new Api($apiKey, $apiSecret);

    // Prepare Razorpay order payload
   /*  $razorpayOrderPayload = [
        'amount' => round($cart->grand_total * 100), // Amount in paise
        'currency' => 'INR',
        'receipt' => 'order_' . uniqid(),
        'notes' => [
            'user_id' => $user->id,
            'email' => $user->email,
        ],
    ];
 */
    $cashfreeOrderId = "";
    $paymentSessionId = "";
    if(!$request->is_cash){
        try {
            // Create Razorpay order
        //  $razorpayOrder = $razorpay->order->create($razorpayOrderPayload);
        // $razorpayOrderId = $razorpayOrder['id'];
            // Generate Cashfree Order ID
            $cashfreeOrderData = $this->generateCashfreeOrder($user, $cart->total_mrp);
            if (!$cashfreeOrderData['success']) {
                return response()->json([
                    'message' => $cashfreeOrderData['message'],
                    'status' => false,
                ], 400);
                exit;
            }
        // dd($cashfreeOrderData);
            $cashfreeOrderId = $cashfreeOrderData['message']['order_id'];
            $paymentSessionId = $cashfreeOrderData['message']['payment_session_id'];
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => "Failed to create Razorpay order: {$e->getMessage()}",
                ],
            ], 500);
        }
    }


    try {
        DB::beginTransaction();

        // Create the order with all required fields
        $order = Order::create([
            'user_id' => $user->id,
            'total_mrp' => $cart->total_mrp,
            'total_tax' => $cart->total_taxes,
            'total_price' => $cart->total_price,
            'total_discount' => $cart->discount,
            'coupon_id' => $cart->coupon_id,
            'grand_total' => $cart->grand_total,
            'status' => 'pending',
            'order_status' => 0,
            'address_id' => $address->id,
            'pg_order_id' => $cashfreeOrderId,
            'pg_session_id' => $paymentSessionId,
        ]);

        // Create order items from cart items
        foreach ($cart->items as $item) {
            $pricing = $this->getVariantPricing($item->variant, $address->pincode);
            
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'qty' => $item->quantity,
                'tax' => $this->calculateTax($pricing),
                'sale_price' => $pricing->price,
                'total_price' => $pricing->price * $item->quantity,
                'delivery_status' => 0
            ]);
        }

        // Soft delete cart and items
        $cart->items()->delete();
        $cart->delete();

        DB::commit();

        return response()->json([
            'order' => $order,
            'pg_order_id' => $cashfreeOrderId,
            'pg_session_id' => $paymentSessionId
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

// Helper function to create Razorpay order
private function createRazorpayOrder($razorpay, $cart)
{
    return $razorpay->order->create([
        'amount' => $cart->grand_total * 100,
        'currency' => 'INR',
        'receipt' => 'order_'.Str::random(10),
    ])['id'];
}

private function generateCashfreeOrder($user, $grandTotal)
{

    try {
        
        $unixTimestamp = now()->timestamp;
        $response = Http::withHeaders([
            'x-client-id' => env('CASHFREE_APP_ID'), // Replace with your APP ID
            'x-client-secret' => env('CASHFREE_SECRET_KEY'), // Replace with your Secret Key
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'x-api-version' => '2023-08-01',
        ])->post(env('CASHFREE_API_URL'), [
            "order_amount" => $grandTotal,
            "order_currency" => "INR",
            "order_id" => $user->id."_".$unixTimestamp,
            "customer_details" => [
                "customer_id" => $user->first_name,
                "customer_phone" => $user->phone,
            ],
            "order_meta" => [
                "return_url" => "https://www.cashfree.com/devstudio/preview/pg/web/checkout?order_id={order_id}",
            ],
        ]);
        
        // Handle the response
        if ($response->successful()) {
            $responseData = $response->json();
            return [
                'success' => true,
                'message' => $responseData,
            ];
            
        } else {
            // Log or handle errors
            return [
                'success' => false,
                'message' => $response->body(),
            ];
        }

    } catch (\Exception $e) {
        return [
            "success"   => false,
            "message"   => "Exception: {$e->getMessage()}",
        ];
    }
}

// Helper function to get variant pricing
private function getVariantPricing($variant, $pincode)
{
    $pricing = Pricing::where('product_sku_id', $variant->sku)
        ->where('pincode', $pincode)
        ->first();

    if (!$pricing) {
        throw new \Exception("Pricing not found for variant {$variant->sku}");
    }

    return $pricing;
}

// Helper function to calculate tax
private function calculateTax($pricing)
{
    return $pricing->tax_type === 0 
        ? ($pricing->price * $pricing->tax_value / 100)
        : $pricing->tax_value;
}


public function getServiceDetails($id){
    // Fetch all active and non-expired coupons
    $service = Service::where('status', 1) // Active coupons only
        ->where('id', '=', $id) // Coupons that have not expired
        ->get();

    if ($service->isEmpty()) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => 'Invalid Service or Property found.',
            ],
            'data' => [],
        ], 404);
    }

    return response()->json([
        'isSuccess' => true,
        'errors' => null,
        'data' => $service,
    ], 200);

}
    /**
     * Update user's first name and last name.
     */
    public function updateProfile(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Get authenticated user
        $user = auth()->user();

        // Update user profile details
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->save();

        return response()->json(['message' => 'Profile updated successfully!', 'user' => $user], 200);
    }

    public function updateProfilePhoto(Request $request)
    {
       
        // Validate the incoming request
        $request['photo'] = $request["_parts"][0][1];
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048', // Ensure it's an image file
        ]);
        dd($request);
        // Get the authenticated user
        $user = auth()->user();

        if ($request->hasFile('photo')) {
            // Retrieve the uploaded file
            $file = $request->file('photo');

            // Generate a unique name for the file
            $fileName = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();

            // Define the destination path in the public folder
            $destinationPath = public_path('profile');

            // Move the file to the destination path
            $file->move($destinationPath, $fileName);

            // Update the user's profile photo path in the database
            $user->profile_photo = 'profile/' . $fileName;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Profile photo updated successfully!',
                'photo_url' => url('profile/' . $fileName),
            ], 200);
        }

        return response()->json([
            'status' => 'failure',
            'message' => 'No image file uploaded!',
        ], 400);
    }



public function orderHistory(Request $request)
{
    $userId = Auth::id();
    
    // Define order status mapping
    $orderStatusMapping = [
        0 => 'Created',
        1 => 'Payment Done',
        2 => 'Order Accept',
        3 => 'Order Preparing',
        4 => 'Order Shipped',
        5 => 'Order Delivered',
        6 => 'Order Completed',
        7 => 'Order Rejected',
        8 => 'Order Returned',
        9 => 'Order Cancelled'
    ];

    // Get orders with item count and format data
    $orders = Order::where('user_id', $userId)
        ->withCount('orderItems')
        ->latest()
        ->get()
        ->map(function ($order) use ($orderStatusMapping) {
            return [
                'id' => $order->id,
                'status' => $orderStatusMapping[$order->order_status] ?? 'Unknown Status',
                'item_count' => $order->order_items_count,
                'total_amount' => $order->grand_total,
                'created_at' => $this->formatTimestamp($order->created_at),
                'order_items' => $order->orderItems->count()
            ];
        });

    return response()->json([
        'status' => 'success',
        'data' => [
            'orders' => $orders,
            'total_orders' => $orders->count()
        ],
        'message' => 'Order history retrieved successfully!',
    ], 200);
}

private function formatTimestamp($createdAt)
{
    $now = Carbon::now();
    $createdDate = Carbon::parse($createdAt);
    
    $diffInDays = $createdDate->diffInDays($now);

    if ($createdDate->isToday()) {
        return 'Today';
    } elseif ($createdDate->isYesterday()) {
        return 'Yesterday';
    } elseif ($diffInDays < 7) {
        return 'This week';
    } elseif ($diffInDays < 30) {
        return 'This month';
    }

    return $createdDate->format('jS F');
}



    public function mobileUpdate(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|digits:10|unique:users,phone,' . auth()->id(), // Assuming you're using Laravel's auth system
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors occurred',
                'errors' => $e->errors(),
            ], 422);
        }

        if (User::where('phone', $request->phone)->exists()) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Phone number already exists.',
                ],
                'data' => [],
            ], 400);
        }


        $user = auth()->user();
        $user->dummy_phone = $request->phone;
        $user->save();

        $otp = rand(100000, 999999);
        $otpCreate = Otp::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'status' => 'pending',
            'expiry' => Carbon::now()->addMinutes(10),
            'complete' => false,
        ]);

        Log::info("OTP for user {$user->phone}: {$otp}");

        $this->sendSms($user->dummy_phone, $otp);

        return response()->json([
            'success' => true,
            'message' => 'Mobile number updated successfully. OTP has been sent to your new mobile number.',
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

    public function emailUpdate(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:users,email',
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

        $email = $request->email;

        // Check if email exists
        if (User::where('email', $email)->exists()) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Email address already exists.',
                ],
                'data' => [],
            ], 400);
        }

        // Update user's email and set email_verified_at to null
        $user = auth()->user();
        $user->dummy_email = $email;
        $user->email_verified_at = null;
        $user->save();

        // Generate and send OTP
        $otp = rand(100000, 999999);
       

        // Optionally store OTP in database
        Otp::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'status' => 'pending',
            'expiry' => Carbon::now()->addMinutes(10),
            'complete' => false,
        ]);
        $this->sendEmailWithOtp($otp);
        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Email updated successfully. OTP has been sent to your new email address.',
            ],
            'data' => [],
        ]);
    }

    // Example method to send email with OTP
    private function sendEmailWithOtp($otp)
    {
        $user = auth()->user();
        Mail::to($user->dummy_email)->send(new VerifyOtpMail($user,$otp));
    }


    public function orderDetail($orderId)
    {
        // Get order with items and variants
        $order = Order::with(['orderItems.variant' => function($query) {
            $query->select('id', 'product_id', 'name', 'sku'); // Add other variant fields you need
        }])->find($orderId);
    
        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404);
        }
    
        // Format response
        $formattedOrder = [
            'order_id' => $order->id,
            'order_placed_at' => $order->created_at->format('Y-m-d H:i:s'),
            'payment_confirmed_at' => !empty($order->payment_confirmed_date) ? $order->payment_confirmed_date->format('Y-m-d H:i:s') : null,
            'order_processed_at' => !empty($order->order_processed_date) ? $order->order_processed_date->format('Y-m-d H:i:s') : null,
            'ready_to_pickup_at' => !empty($order->ready_to_pickup_date) ? $order->ready_to_pickup_date->format('Y-m-d H:i:s') : null,
            'delivered_at' => !empty($order->order_delevered_date) ? $order->order_delevered_date->format('Y-m-d H:i:s') : null,
            'cancelled_at' => !empty($order->order_cancelled_date) ? $order->order_cancelled_date->format('Y-m-d H:i:s') : null,
            'total_amount' => $order->grand_total,
            'items' => $order->orderItems->map(function($item) {
                return [
                    'item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->qty,
                    'price' => $item->sale_price,
                    'variant_details' => $item->variant ? [
                        'variant_name' => $item->variant->name,
                        'sku' => $item->variant->sku,
                        'price' => $item->variant->price,
                        'product_id' => $item->variant->product_id
                    ] : null
                ];
            })
        ];
    
        return response()->json([
            'status' => 'success',
            'data' => $formattedOrder
        ], 200);
    }
    
    public function orderTransaction($orderId){
        return response()->json([
            'status' => 'success',
            'message' => 'Profile photo updated successfully!',
        ], 200);
    }

    public function verifyMobile(Request $request){
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);
        $user = auth()->user();
        
        $otpRecord = Otp::where('user_id', $user->id)
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
    
        $user->phone =  $user->dummy_phone;
        $user->dummy_phone = null;
        $user->save();

        $user = User::findOrFail($user->id);
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
    }

    public function verifyEmail(Request $request){
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);
        $user = auth()->user();
        
        $otpRecord = Otp::where('user_id', $user->id)
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
    
        $user->email =  $user->dummy_email;
        $user->dummy_email = null;
        $user->email_verified_at = null;
        $user->markEmailAsVerified();
        $user->save();

        $user = User::findOrFail($user->id);
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
    }

    public function searchVendorOrder(Request $request, $orderId)
    {
        // Get the authenticated user
        $user = $request->user();

        $order = Order::with(['orderItems.product','orderItems.variant'])
            ->where('id', $orderId)
            ->where('vendor_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Order not found or you do not have access to this order.',
                ],
                'data' => null,
            ], 404);
        }
        $totalProducts = [];
        $order->orderItems->each(function ($item) use (&$totalProducts) {
            $totalProducts[] = $item;
        });

        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => null,
            ],
            'data' => [
                'order_id' => $order->id,
                'vendor_id' => $order->vendor_id,
                'total_amount' => $order->grand_total,
                'status' => $order->status,
                'created_at' => $order->created_at,
                'updated_at' => $order?->updated_at,
                'order_items' => $totalProducts, // Include mapped order items
            ],
        ], 200);
    }
    


private function __getProductDetail(Request $request, $productId)
{
    // Fetch the product by ID with its relationships
    $product = Product::with('category', 'subcategory', 'variants')->find($productId);

    // Check if the product exists
    if (!$product) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => 'Product not found.',
            ],
            'data' => null,
        ], 404);
    }

    // Convert the image field to an array
    $images = $product->image ? explode(',', $product->image) : [];

    // Get the authenticated user's default address
    $user = $request->user(); // Assuming user is authenticated
    $defaultPincode = null;

    if ($user && $user->default_address) {
        $address = Address::find($user->default_address);
        if ($address) {
            $defaultPincode = $address->pincode;
        }
    }

    // Safely map variants with pricing based on pincode
    $variants = optional($product)->variants->map(function ($variant) use ($defaultPincode, $product) {
        // Fetch pricing based on pincode
        $pricing = null;
        if ($defaultPincode) {
            $pricing = Pricing::where('pincode', $defaultPincode)
                ->where('product_id', $product->id)
                ->where('product_sku_id', $variant->id)
                ->where('status', 1)
                ->first();
        }

        // Map the type field to human-readable values
        $typeMapping = [
            1 => 'Quality',
            2 => 'Colour',
            3 => 'Size',
        ];
        $typeName = $typeMapping[$variant->type] ?? 'Unknown'; // Default to "Unknown" if type is not mapped

        return [
            'id' => $variant->id,
            'name' => $variant->name,
            'sku' => $variant->sku,
            'short_description' => $variant->short_description,
            'min_quantity' => $variant->min_quantity,
            'type' => $typeName, // Add the mapped type name
            'price' => $pricing ? [
                'mrp' => $pricing->mrp,
                'price' => $pricing->price,
                'tax_type' => $pricing->tax_type,
                'tax_value' => $pricing->tax_value,
                'ship_charges' => $pricing->ship_charges,
                'valid_upto' => $pricing->valid_upto,
                'is_cash' => $pricing->is_cash,
            ] : null, // If no pricing is found, return null
        ];
    });

    // Prepare the response with all necessary data
    return response()->json([
        'isSuccess' => true,
        'errors' => [
            'message' => null,
        ],
        'data' => [
            ...$product->toArray(),
            'image' => $images,
            'category' => [
                'id' => optional($product->category)->id, // Use optional chaining to avoid errors if category is null
                'name' => optional($product->category)->name,
            ],
            'subcategory' => [
                'id' => optional($product->subcategory)->id, // Use optional chaining to avoid errors if subcategory is null
                'name' => optional($product->subcategory)->name,
            ],
            'variants' => $variants, // Include safely mapped variants
        ],
    ], 200);
}


public function vendorDashboard(Request $request)
{
    $user = $request->user();

    // Fetch latest 5 orders for this vendor with related order items, products, and variants
    $orders = Order::with(['orderItems.product', 'orderItems.variant'])
        ->where('vendor_id', $user->id)
        ->latest() // optional: latest orders first
        ->limit(5)
        ->get();

    if ($orders->isEmpty()) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => 'No orders found for this vendor.',
            ],
            'data' => null,
        ], 404);
    }

    // Initialize counters
    $newOrder = 0;
    $processing = 0;
    $completeOrder = 0;
    $rejectOrder = 0;

    // Count orders based on status (assuming you have a status field)
    foreach ($orders as $order) {
    //    dd($order->status);
        switch ($order->status) {
            case 'pending':
                $newOrder++;
                break;
            case 'processing':
                $processing++;
                break;
            case 'completed':
                $completeOrder++;
                break;
            case 'rejected':
                $rejectOrder++;
                break;
        }
    }

    // Map orders with their items clearly
    $ordersData = $orders->map(function ($order) {
        return [
            'order_id' => $order->id,
            'status' => $order->status,
            'total_amount' => $order->grand_total,
            'created_at' => $order->created_at,
            'items' => $order->orderItems->map(function ($item) {
                
                return [
                    'item_id' => $item->id,
                    'product' => $item->product,
                    'variant' => $item->variant,
                    'quantity' => $item->qty,
                    'price' => $item->sale_price,
                    'tax' => $item->tax,
                ];
            }),
        ];
    });

    return response()->json([
        'isSuccess' => true,
        'errors' => [
            'message' => null,
        ],
        'data' => [
            'new_order'      => $newOrder,
            'process_order'  => $processing,
            'complete_order' => $completeOrder,
            'reject_order'   => $rejectOrder, 
            'orders'         => $ordersData, // Include all mapped orders with items
        ],
    ], 200);
}

public function vendorOrders(Request $request)
{
    $user = $request->user();

    // Define mapping of numeric statuses to header names
    $statusHeaders = [
        0 => 'Created',
        1 => 'Payment Done',
        2 => 'Order Accept',
        3 => 'Order Preparing',
        4 => 'Order Shipped',
        5 => 'Order Delivered',
        6 => 'Order Completed',
        7 => 'Order Rejected',
        8 => 'Order Returned',
        9 => 'Order Cancelled'
    ];

    // Retrieve filters from request
    $limit = $request->input('limit', 5);
    $offset = $request->input('offset', 0);
    $orderDirection = $request->input('order_direction', 'desc'); // default descending
    $statusFilter = $request->input('order_status', null); // numeric status filter

    // Base query with relationships
    $query = Order::with(['orderItems.product', 'orderItems.variant'])
                ->where('vendor_id', $user->id);
 
    // Apply status filter if provided
    if (!is_null($statusFilter) && array_key_exists($statusFilter, $statusHeaders)) {
        $query->where('order_status', $statusFilter);
        $header_name = $statusHeaders[$statusFilter];
    } else {
        $header_name = "All Orders";
    }

    // Get total count before pagination
    $totalOrders = $query->count();

    // Apply ordering, limit, and offset for pagination
    $limit = (int) $request->input('limit', 5); // default limit is 5
    $offset = (int) $request->input('offset', 0);

    $orders = $query->orderBy('created_at', $orderDirection)
                    ->limit($limit)
                    ->offset($offset = (int) $request->input('offset', 0))
                    ->get();

    if ($orders->isEmpty()) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => 'No orders found for this vendor.',
            ],
            'data' => null,
        ], 404);
    }

    // Map orders data clearly
    $ordersData = $orders->map(function ($order) {
        return [
            'order_id'     => $order->id,
            'status'       => $order->order_status,
            'total_amount'  => $order->grand_total,
            'created_at'   => $order->created_at,
            'items'          => $order->orderItems->map(function ($item) {
                return [
                    'item_id'   => $item->id,
                    'product'   => $item->product,
                    'variant'   => $item->variant,
                    'quantity'  => $item->quantity,
                    'price'     => $item->price,
                ];
            }),
        ];
    });

    return response()->json([
        'isSuccess' => true,
        'errors' => [
            'message' => null,
        ],
        'data' => [
            'header_name'   => isset($header_name) ? $header_name : "All Orders",
            'total_orders'   => $totalOrders,
            'limit'                 => $limit,
            'offset'               => (int)$request->input('offset', 0),
            'orders'              => $ordersData,
        ],
    ], 200);
}


public function getVendorStocks()
{
    // Fetch variants with their associated products
    $variants = Variant::with('Product')->get();

    // Check if there are no variants
    if ($variants->isEmpty()) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => 'No products.',
            ],
            'data' => null,
        ], 404);
    }

    // Group variants by product and format the response
    $products = $variants->filter(function ($variant) {
        // Only include variants with a valid product
        return $variant->product !== null;
    })->groupBy('product.id')->map(function ($groupedVariants, $productId) {
        $product = $groupedVariants->first()->product; // Get product details from the first variant

        // Prepare variants for each product
        $formattedVariants = $groupedVariants->map(function ($variant) {
            // Fetch seller price details for the current variant and logged-in user
            $sellerPrice = SellerPrice::where('user_id', auth()->id())
                ->where('variant_id', $variant->id)
                ->first();

            return [
                'variant_id' => $variant->id,
                'name' => $variant->name,
                'old_qty' => optional($sellerPrice)->quantity ?? 0, // Default to 0 if no record found
                'old_price' => optional($sellerPrice)->prices ?? 0.0, // Default to 0.0 if no record found
            ];
        });

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'variants' => $formattedVariants,
        ];
    })->values(); // Reset array keys

    return response()->json([
        'isSuccess' => true,
        'errors' => [
            'message' => null,
        ],
        'data' => $products,
    ], 200);
}




public function acceptRejectOrder(Request $request)
{
    // Validate request input
    $validated = $request->validate([
        'order_id'  => 'required|integer|exists:orders,id',
        'type'      => 'required', // 0 for reject, 1 for accept
        'reason'    => 'nullable|string',
    ]);

    $userId = Auth::id();
    // Find or create the OrderReason record
    $orderReason = OrderReason::updateOrCreate(
        [
            'order_id' => $validated['order_id'],
            'user_id'  => $userId,
            'type'     => $validated['type'],
        ],
        [
            'reason'   => $validated['reason'] ?? null,
        ]
    );

    // Update order status based on type
    $order = Order::findOrFail($validated['order_id']);

    if ($validated['type'] == 0) { // Rejected
        $order->order_status = 7; // Rejected status code
    } else if ($validated['type'] == 1){ // Accepted
        $order->order_status = 2; // Accepted status code
    }else if ($validated['type'] == 3){ // Accepted
        $order->order_status = 3; // Accepted status code
    }else if ($validated['type'] == 4){ // Accepted
        $order->order_status = 4; // Accepted status code
    }else if ($validated['type'] == 5){ // Accepted
        $order->order_status = 5; // Accepted status code
    }



    $order->save();

    return response()->json([
        'success' => true,
        'message' => ($validated['type'] ? "Order accepted successfully." : "Order rejected successfully."),
        'data' => [
            'order_reason' => $orderReason,
            'order_status' => $order->order_status,
        ],
    ]);
}


public function updateSellerPrices(Request $request)
{
    // Validate request data
    $request->validate([
        'variant_id' => 'required|integer|exists:variants,id',
        'quantity' => 'required|integer|min:1',
        'prices' => 'required|numeric|min:0',
    ]);
    // Get logged-in user ID from JWT token
    $userId = Auth::id();

    // Check if a record exists for the same day
    $today = now()->startOfDay();
    $existingRecord = SellerPrice::where('user_id', $userId)
        ->where('variant_id', $request->variant_id)
        ->whereDate('created_at', '=', $today)
        ->first();

    if ($existingRecord) {
        // Update existing record
        $existingRecord->update([
            'quantity' => $request->quantity,
            'prices' => $request->prices,
        ]);
    } else {
        // Create a new record
        SellerPrice::create([
            'user_id' => $userId,
            'variant_id' => $request->variant_id,
            'quantity' => $request->quantity,
            'prices' => $request->prices,
        ]);
    }

    return response()->json(['message' => 'Seller price updated successfully.']);
}

public function getUserNotifications(Request $request)
{
    // Validate query parameters
    $request->validate([
        'limit' => 'integer|min:1|max:100',
        'offset' => 'integer|min:0',
        'sort_by' => 'in:created_at,type',
        'sort_order' => 'in:asc,desc',
    ]);

    // Get logged-in user ID
    $userId = Auth::id();

    // Set default values for limit, offset, and sorting
    $limit = $request->get('limit', 10); // Default limit is 10
    $offset = $request->get('offset', 0); // Default offset is 0
    $sortBy = $request->get('sort_by', 'created_at'); // Default sort by created_at
    $sortOrder = $request->get('sort_order', 'desc'); // Default sort order is descending

    // Fetch notifications for the user with pagination and sorting
    $notifications = Notification::where('user_id', $userId)
        ->orderBy($sortBy, $sortOrder)
        ->skip($offset)
        ->take($limit)
        ->get();

    return response()->json([
        'data' => $notifications,
        'message' => 'Notifications fetched successfully.',
    ]);
}

public function getUserOrders(Request $request)
{
    $userId = Auth::id();

    $orderStatusMapping = [
        0 => 'Created',
        1 => 'Payment Done',
        2 => 'Order Accept',
        3 => 'Order Preparing',
        4 => 'Order Shipped',
        5 => 'Order Delivered',
        6 => 'Order Completed',
        7 => 'Order Rejected',
        8 => 'Order Returned',
        9 => 'Order Cancelled'
    ];
    // Set default values for limit, offset, and sorting
    $limit = $request->get('limit', 10); // Default limit is 10
    $offset = $request->get('offset', 0); // Default offset is 0
    $sortBy = $request->get('sort_by', 'created_at'); // Default sort by created_at
    $sortOrder = $request->get('sort_order', 'desc'); // Default sort order is descending

    // Base query with customer relationship
    $query = Order::where('vendor_id', $userId)
        ->with(['users' => function ($q) {
            $q->select('id', 'first_name'); // Fetch only required fields
        }]);

    // Filter orders by date range if "days" parameter is provided
    if ($request->has('days')) {
        $days = $request->get('days');
        $query->where('created_at', '>=', now()->subDays($days));
    }

    // Fetch transactions with pagination, sorting, and customer name
    $orders['transactions'] = $query
        ->select('id', 'order_status', 'grand_total', 'user_id','vendor_id')
        ->orderBy($sortBy, $sortOrder)
        ->offset($offset)
        ->limit($limit)
        ->get()
        ->map(function ($order) use ($orderStatusMapping) {
            return [
                'id' => $order->id,
                'order_status' => $orderStatusMapping[$order->order_status] ?? 'Unknown Status',
                'grand_total' => $order->grand_total,
                'customer' => optional($order->users)->first_name.' '.optional($order->users)->last_name ?? 'N/A', // Handle missing customer gracefully
            ];
        });

    // Calculate grand total of all completed orders (order_status = 6)
    $orders['total'] = Order::where('vendor_id', $userId)
    ->whereIn('order_status', [5, 6]) // Check for order_status 5 or 6
    ->sum('grand_total');
    return response()->json([
        'data' => $orders,
        'message' => 'Orders fetched successfully.',
    ]);
}


public function getInvoice(Request $request, $orderId)
{
    $order = Order::findOrFail($orderId);

    if (!$order) {  
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => 'Order not found.',
            ],
        ], 404);
    }             

    $orderItems = "http://xyz.com/invoice/".$orderId;   

    return response()->json([
        'data' => $orderItems,
        'message' => 'Invoice fetched successfully.',
    ]);

}

}