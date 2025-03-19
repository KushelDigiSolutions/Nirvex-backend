<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'SubCategory'])->orderBy('id','desc')->get();
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::with('subcategories')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */

     public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'cat_id' => 'required|integer',
            'sub_cat_id' => 'required|integer',
            'status' => 'required|boolean',
            'mrp' => 'required|numeric',
            'availability' => 'required|string',
            'specification' => 'required|string',
            'return_policy' => 'required|string',
            'physically_property' => 'required|string',
            'standard' => 'required|string',
            'benefits' => 'required|string',
            'image.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:1024',
            'options.*.type' => 'required|string',
            'options.*.name' => 'required|string|max:255',
            'options.*.description' => 'nullable|string|max:255',
            'options.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'options.*.sku' => [
            'required',
            'string',
            'max:255',
                function ($attribute, $value, $fail) {
                    if (\DB::table('variants')->where('sku', $value)->exists()) {
                        $fail("The SKU '$value' is already taken.");
                    }
                },
            ],
        ]);
       if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                if ($file->getSize() > 1024 * 1024) { // 1MB limit
                    return redirect()->back()->withErrors(['image' => 'Each image must not be greater than 1MB.']);
                }
            }
        }

        $imagePaths = [];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $fileName = time() . '_' . $image->getClientOriginalName();
                $path = $image->move(public_path('uploads/products'), $fileName);
                $imagePaths[] = 'uploads/products/' . $fileName;
            }
        }
        $validatedData['image'] = implode(',', $imagePaths);


        $product = Product::create([
            'name'             => $validatedData['name'],
            'description'      => $validatedData['description'] ?? null,
            'cat_id'           => $validatedData['cat_id'],
            'sub_cat_id'       => $validatedData['sub_cat_id'],
            'status'           => $validatedData['status'],
            'mrp'              => $validatedData['mrp'],
            'availability'     => $validatedData['availability'],
            'return_policy'    => $validatedData['return_policy'] ?? null,
            'physical_property'=> $validatedData['physically_property'] ?? null,
            'key_benefits'     => $validatedData['key_benefits'] ?? 'Default Benefits', 
            'specification'    => $validatedData['specification'] ?? null,
            'standards'        => $validatedData['standard'] ?? null,
            'image'            => $validatedData['image'] ?? null,
        ]);

        // Create the product
        // $product = Product::create($validatedData);
        // dd($product);
        // Process dynamic form values for variants
        if ($request->has('options')) {
            foreach ($request->options as $option) {
                // Prepare variant data
                $variantData = [
                    'product_id' => $product->id,
                    'name' => $option['name'],
                    'sku'  => $option['sku'],
                    'type' => $option['type'],
                    'short_description' => $option['short_description'] ?? null,
                ];

                // dd($variantData);
                
                if (isset($option['image']) && $option['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $fileName = time() . '_' . $option['image']->getClientOriginalName();
                    $path = $option['image']->move(public_path('uploads/variants'), $fileName);
                    $variantData['images'] = 'uploads/variants/' . $fileName;
                }

                $variant = Variant::create($variantData);
                $variant->save();
            }
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

     
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(string $id)
    {
         $categories = Category::all();
         $product = Product::with(['category', 'SubCategory', 'variants'])->find(decrypt($id));
        
        if ($product && $product->image) {
            $product->image = explode(',', $product->image);
        }
        
        $subCategories = SubCategory::get()->where('cat_id',$product->cat_id)->all();
     
        return view('admin.products.edit', compact('categories', 'subCategories', 'product'));
    }

    

    public function update(Request $request, $id)
    {
        // Find the product
        $product = Product::findOrFail($id);
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'descriptions' => 'nullable|string',
                'cat_id' => 'required|integer',
                'sub_cat_id' => 'required|integer',
                'status' => 'required|boolean',
                'mrp' => 'required|numeric',
                'availability' => 'required|string',
                'specification' => 'required|string',
                'return_policy' => 'required|string',
                'physical_property' => 'required|string',
                'standards' => 'required|string',
                'key_benefits' => 'required|string',
                'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
                'options.*.id' => 'nullable|integer', 
                'options.*.type' => 'required|string',
                'options.*.name' => 'required|string|max:255',
                'options.*.short_description' => 'nullable|string|max:255',
                'options.*.sku' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('variants', 'sku'),
                ],
                'options.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            ]);
        
            // If validation passes, dump data
            dd($validatedData);
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors()); // Show validation errors if any
        }
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                if ($file->getSize() > 1024 * 1024) { // 1MB limit
                    return redirect()->back()->withErrors(['image' => 'Each image must not be greater than 1MB.']);
                }
            }
        }

        $imagePaths = [];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $fileName = time() . '_' . $image->getClientOriginalName();
                $path = $image->move(public_path('uploads/products'), $fileName);
                $imagePaths[] = 'uploads/products/' . $fileName;
            }
        }
        if (!empty($imagePaths)) {
            $validatedData['image'] = implode(',', $imagePaths);
        } else {
            unset($validatedData['image']);
        }

        $product->update($validatedData);

        if ($request->has('options')) {
            foreach ($request->options as $option) {
                if (isset($option['id'])) {
                    $variant = Variant::findOrFail($option['id']);
                    $variantData = [
                        'name' => $option['name'],
                        'type' => $option['type'],
                        'sku' => $option['sku'],
                        'short_description' => $option['short_description'] ?? null,
                    ];

                    if (isset($option['image']) && $option['image'] instanceof \Illuminate\Http\UploadedFile) {
                        $fileName = time() . '_' . $option['image']->getClientOriginalName();
                        $path = $option['image']->move(public_path('uploads/variants'), $fileName);
                        $variantData['images'] = 'uploads/variants/' . $fileName;
                    }

                    $variant->update($variantData);
                } else {
                    $variantData = [
                        'product_id' => $product->id,
                        'name' => $option['name'],
                        'type' => $option['type'],
                        'sku' => $option['sku'],
                        'short_description' => $option['short_description'] ?? null,
                    ];

                    if (isset($option['image']) && $option['image'] instanceof \Illuminate\Http\UploadedFile) {
                        $fileName = time() . '_' . $option['image']->getClientOriginalName();
                        $path = $option['image']->move(public_path('uploads/variants'), $fileName);
                        $variantData['images'] = 'uploads/variants/' . $fileName;
                    }
                    // $variant = Variant::create($variantData);
                    // $variant->save();
                    Variant::updateOrCreate(
                        ['id' => $option['id'] ?? null], 
                        $variantData
                    );
                }
            }
        }

        return redirect()->route('products.index', ['product' => $product->id])->with('success', 'Product updated successfully.');
    }

     


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Product::where('id',decrypt($id))->delete();
        return redirect()->back()->with('success','Product deleted successfully.');
        
    }

    public function deleteImage(Request $request)
{
    $imagePath = $request->input('image_path');

    if (file_exists(public_path($imagePath))) {
        unlink(public_path($imagePath));
    }

    $product = Product::find($request->input('product_id')); 
    if ($product) {
        $images = explode(',', $product->image);
        $updatedImages = array_filter($images, fn($img) => $img !== $imagePath);
        $product->image = implode(',', $updatedImages); 
        $product->save();
    }

    return back()->with('success', 'Image deleted successfully.');
}

public function search(Request $request)
{
    $query = $request->input('query');
    // $products = DB::table('products')
    //     ->where('name', 'LIKE', "%{$query}%")
    //     ->select('id', 'name')
    //     ->get();

    // $products = Product::where('name', 'LIKE', '%' . $query . '%')
    // ->with(['variants:id,product_id,name,sku'])
    // ->first();
    $products = Product::with('variants')->find($request->product_id);

    if (!$products) {
        return response()->json(null, 404);
    }
    // echo '<pre>'; print_r($products); die;

    return response()->json($products);
}

public function fetchVariants(Request $request)
{
    $productId = $request->input('product_id');

    if ($productId) {
        $variants = DB::table('variants')
            ->where('variants.product_id', $productId)
            ->select('variants.id', 'variants.name', 'variants.type', 'variants.sku')
            ->get();

        return response()->json($variants);
    }

    return response()->json([]);
}



// public function search(Request $request)
// {
//     $query = $request->input('query');

//     if ($query) {
//             $products = Product::where('name', 'LIKE', '%' . $query . '%')
//                            ->limit(10)
//                            ->get(['id', 'name']);
//         return response()->json($products);
//     }

//     return response()->json([]);
// }

}
