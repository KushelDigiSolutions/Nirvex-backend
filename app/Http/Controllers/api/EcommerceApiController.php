<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use App\Models\Product;
use App\Models\Pricing;
use App\Models\Slider;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Rating;
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
            }
        }

        // Prepare variants with pricing
        $variants = $product->variants->map(function ($variant) use ($defaultPincode, $product) {
            // Fetch pricing based on pincode
            $pricing = null;
            if ($defaultPincode) {
                $pricing = Pricing::where('pincode', $defaultPincode)
                    ->where('product_id', $product->id)
                    ->where('product_sku_id', $variant->sku)
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
    


    public function deleteFromCart(Request $request, $itemId)
    {
        $user = $request->user();
    
        // Find the cart item by ID and ensure it belongs to the user's cart
        $cartItem = CartItem::whereHas('cart', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->find($itemId);
    
        if (!$cartItem) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Cart item not found.',
                ],
            ], 404);
        }
    
        // Delete the cart item
        $cartItem->delete();
    
        // Count the total number of distinct variants in the cart after deletion
        $itemCount = CartItem::where('cart_id', $cartItem->cart_id)->count();
    
        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Item removed from cart successfully.',
            ],
            'data' => [
                'item_count' => $itemCount, // Include count of items in the cart
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
    
            // Add product details to the response object
            $productPricingDetails[] = [
                'variant_id' => $item->variant_id,
                'product_id' => $item->product_id,
                'name' => $item->variant->name,
                'sku' => $item->variant->sku,
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


}
