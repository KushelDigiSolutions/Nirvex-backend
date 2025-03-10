<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            'return' => 'required|string',
            'physically_property' => 'required|string',
            'standard' => 'required|string',
            'benefits' => 'required|string',
            'image.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'options.*.type' => 'required|string',
            'options.*.name' => 'required|string|max:255',
            'options.*.description' => 'nullable|string|max:255',
            'options.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        // Process product images
        $imagePaths = [];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $fileName = time() . '_' . $image->getClientOriginalName();
                $path = $image->move(public_path('uploads/products'), $fileName);
                $imagePaths[] = 'uploads/products/' . $fileName;
            }
        }
        $validatedData['image'] = implode(',', $imagePaths);

        // Create the product
        $product = Product::create($validatedData);

        // Process dynamic form values for variants
        if ($request->has('options')) {
            foreach ($request->options as $option) {
                // Prepare variant data
                $variantData = [
                    'product_id' => $product->id,
                    'name' => $option['name'],
                    'type' => $option['type'],
                    'short_description' => $option['short_description'] ?? null,
                ];

                // Process variant image
                if (isset($option['image']) && $option['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $fileName = time() . '_' . $option['image']->getClientOriginalName();
                    $path = $option['image']->move(public_path('uploads/variants'), $fileName);
                    $variantData['images'] = 'uploads/variants/' . $fileName;
                }

                // Create the variant in the database
                $variant = Variant::create($variantData);

                // Update the SKU with the correct format (NVX0.{product.id}.{variant.id})
                $variant->sku = "NVX0.{$product->id}.{$variant->id}";
                $variant->save();
            }
        }

        return redirect()->route('products.create')->with('success', 'Product created successfully.');
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
            'return' => 'required|string',
            'physically_property' => 'required|string',
            'standard' => 'required|string',
            'benefits' => 'required|string',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'options.*.id' => 'nullable|integer', // For updating existing variants
            'options.*.type' => 'required|string',
            'options.*.name' => 'required|string|max:255',
            'options.*.description' => 'nullable|string|max:255',
            'options.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        // Process product images
        $imagePaths = [];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $fileName = time() . '_' . $image->getClientOriginalName();
                $path = $image->move(public_path('uploads/products'), $fileName);
                $imagePaths[] = 'uploads/products/' . $fileName;
            }
        }

        // If new images are uploaded, update the image field; otherwise, keep existing images
        if (!empty($imagePaths)) {
            $validatedData['image'] = implode(',', $imagePaths);
        } else {
            unset($validatedData['image']);
        }

        // Update the product
        $product->update($validatedData);

        // Process dynamic form values for variants
        if ($request->has('options')) {
            foreach ($request->options as $option) {
                if (isset($option['id'])) {
                    // Update existing variant
                    $variant = Variant::findOrFail($option['id']);
                    $variantData = [
                        'name' => $option['name'],
                        'type' => $option['type'],
                        'short_description' => $option['short_description'] ?? null,
                    ];

                    // Process variant image if provided
                    if (isset($option['image']) && $option['image'] instanceof \Illuminate\Http\UploadedFile) {
                        $fileName = time() . '_' . $option['image']->getClientOriginalName();
                        $path = $option['image']->move(public_path('uploads/variants'), $fileName);
                        $variantData['images'] = 'uploads/variants/' . $fileName;
                    }

                    // Update the variant in the database
                    $variant->update($variantData);
                } else {
                    // Create a new variant
                    $variantData = [
                        'product_id' => $product->id,
                        'name' => $option['name'],
                        'type' => $option['type'],
                        'short_description' => $option['short_description'] ?? null,
                    ];

                    // Process variant image
                    if (isset($option['image']) && $option['image'] instanceof \Illuminate\Http\UploadedFile) {
                        $fileName = time() . '_' . $option['image']->getClientOriginalName();
                        $path = $option['image']->move(public_path('uploads/variants'), $fileName);
                        $variantData['images'] = 'uploads/variants/' . $fileName;
                    }

                    // Create the new variant in the database
                    $variant = Variant::create($variantData);

                    // Update the SKU with the correct format (NVX0.{product.id}.{variant.id})
                    $variant->sku = "NVX0.{$product->id}.{$variant->id}";
                    $variant->save();
                }
            }
        }

        return redirect()->route('products.edit', ['product' => $product->id])->with('success', 'Product updated successfully.');
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

    $products = Product::where('name', 'LIKE', '%' . $query . '%')
    ->with(['variants:id,product_id,name,sku'])
    ->first();

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
