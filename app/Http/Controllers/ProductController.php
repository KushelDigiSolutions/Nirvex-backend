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
         $subCategories = SubCategory::all();
         $products = Product::with(['category', 'SubCategory', 'variants'])->find(decrypt($id));
        //  dd($products);
            if ($products && $products->image) {
                $products->image = explode(',', $products->image);
        }
        return view('admin.products.edit', compact('categories', 'subCategories', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */

     public function updatelatest020325(Request $request, string $id)
     {
         $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'cat_id' => 'required|integer',
            'sub_cat_id' => 'required|integer',
            'status' => 'required|boolean',
            'mrp' => 'required|numeric',
            'availability' => 'required|string',
            'specification' => 'required|string',
            'options.*.type' => 'required|string',
            'options.*.name' => 'required|string|max:255',
            'options.*.description' => 'nullable|string|max:255',
            'options.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'image' => 'nullable|array', 
            'image.*' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
         ]);
         try {
            $product = Product::withTrashed()->findOrFail($id);
           if(empty($request->delete_images)){
                $existingImages = explode(',', $product->image);
                foreach ($existingImages as $image) {
                    if (!empty($image) && file_exists(public_path($image))) {
                        unlink(public_path($image));
                    }
                }
                $product->image = null;
            }else if($request->has('delete_images') && is_array($request->delete_images)) {
                $existingImages = explode(',', $product->image); 
                $imagesToKeep = $request->delete_images; 
                $imagesToDelete = array_diff($existingImages, $imagesToKeep);
                foreach ($imagesToDelete as $image) {
                    if (file_exists(public_path($image))) {
                        unlink(public_path($image));
                    }
                }
                $product->image = implode(',', $imagesToKeep);
            } 
            if ($request->hasFile('image')) {
                $newImagePaths = [];
                foreach ($request->file('image') as $image) {
                    $fileName = time() . '_' . $image->getClientOriginalName();
                    $path = $image->move(public_path('uploads/products'), $fileName);
                    $newImagePaths[] = 'uploads/products/' . $fileName;
                }
                if (!empty($product->image)) {
                    $existingImages = explode(',', $product->image);
                    $newImagePaths = array_merge($existingImages, $newImagePaths); 
                }
            $product->image = implode(',', $newImagePaths);
            }
             $product->name = $validatedData['name'];
             $product->description = $validatedData['description'];
             $product->cat_id = $validatedData['cat_id'];
             $product->sub_cat_id = $validatedData['sub_cat_id'];
             $product->status = (bool) $validatedData['status'];
             $product->availability = $validatedData['availability'];
             $product->mrp = $validatedData['mrp'];
             $product->return_policy = $validatedData['return_policy'];
            //  $product->sku = $validatedData['sku'];
             $product->specification = $validatedData['specification']; 
             $product->min_quantity = $validatedData['min_quantity'];
             $product->hsn = $validatedData['hsn'];
             $product->gst = (float) $validatedData['gst']; 
             $product->weight = (float) $validatedData['weight']; 
            if (isset($validatedData['weight_type'])) {
                 $product->weight_type = $validatedData['weight_type'];
             } else {
                $product->weight_type = 0;
             }
             if (isset($newImagePaths)) {
                $product->image = implode(',', array_filter($newImagePaths));
             }
             if (!$product->save()) {
                 throw new \Exception('Failed to save the product.');
             }
             return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
         } catch (\Exception $e) {
             \Log::error('Error updating product: '.$e->getMessage());          
            return redirect()
                  ->back()
                  ->withErrors(['message'=>__("Try again later. Error Details".$e)]);
         }
     }


     public function update02032025(Request $request, string $id)
     {
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
        
         try {
             $product = Product::withTrashed()->findOrFail($id);
           
           if(empty($request->delete_images)){
                $existingImages = explode(',', $product->image);
                foreach ($existingImages as $image) {
                    if (!empty($image) && file_exists(public_path($image))) {
                        unlink(public_path($image));
                    }
                }
                $product->image = null;
            }else if($request->has('delete_images') && is_array($request->delete_images)) {
                $existingImages = explode(',', $product->image); 
                $imagesToKeep = $request->delete_images; 
                $imagesToDelete = array_diff($existingImages, $imagesToKeep);
                foreach ($imagesToDelete as $image) {
                    if (file_exists(public_path($image))) {
                        unlink(public_path($image));
                    }
                }
                $product->image = implode(',', $imagesToKeep);
            } 
            if ($request->hasFile('image')) {
                $newImagePaths = [];
                foreach ($request->file('image') as $image) {
                    $fileName = time() . '_' . $image->getClientOriginalName();
                    $path = $image->move(public_path('uploads/products'), $fileName);
                    $newImagePaths[] = 'uploads/products/' . $fileName;
                }
                if (!empty($product->image)) {
                    $existingImages = explode(',', $product->image);
                    $newImagePaths = array_merge($existingImages, $newImagePaths); 
                }
                $product->image = implode(',', $newImagePaths);
            }
             $product->name = $validatedData['name'];
             $product->description = $validatedData['description'];
             $product->cat_id = $validatedData['cat_id'];
             $product->sub_cat_id = $validatedData['sub_cat_id'];
             $product->status = $validatedData['status'];
             $product->mrp = $validatedData['mrp'];
             $product->availability = $validatedData['availability'];
             $product->return_policy = $validatedData['return_policy'];
             $product->specification = $validatedData['specification'];
             $product->physical_property = $validatedData['physical_property'];
             $product->standards = $validatedData['key_benefits'];
             if (isset($newImagePaths)) {
                 $product->image = implode(',', array_filter($newImagePaths));
             }
             if (!$product->save()) {
                 throw new \Exception('Failed to save the product.');
             }     
             return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
         } catch (\Exception $e) {
             \Log::error('Error updating product: '.$e->getMessage());
            return redirect()
                  ->back()
                  ->withErrors(['message'=>__("Try again later. Error Details".$e)]);
         }
     }

    public function update(Request $request, string $id)
     {
        \Log::info('Update method triggered');
        \Log::info($request->all());
    
        // dd($request->all());
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'cat_id' => 'required|integer',
            'sub_cat_id' => 'required|integer',
            'status' => 'required|boolean',
            'mrp' => 'required|numeric',
            'availability' => 'required|string',
            'specification' => 'required|string',
            'options.*.type' => 'required|string',
            'options.*.name' => 'required|string|max:255',
            'options.*.description' => 'nullable|string|max:255',
            'options.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'image' => 'nullable|array', 
            'image.*' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
         ]);

         try {
             $product = Product::findOrFail($id);
             $imagePaths = [];
             if ($request->hasFile('image')) {
                 foreach ($request->file('image') as $image) {
                     $fileName = time() . '_' . $image->getClientOriginalName();
                     $path = $image->move(public_path('uploads/products'), $fileName);
                     $imagePaths[] = 'uploads/products/' . $fileName;
                 }
                 $request['image'] = implode(',', $imagePaths);
             }
             $product->update($request);
             if ($request->has('options')) {
                 $existingVariantIds = $product->variants->pluck('id')->toArray();
     
                 foreach ($request->options as $index => $option) {
                     if (isset($option['id']) && in_array($option['id'], $existingVariantIds)) {
                         $variant = $product->variants()->find($option['id']);
                         $variantData = [
                             'type' => $option['type'],
                             'name' => $option['name'],
                             'short_description' => $option['short_description'] ?? null,
                             'sku' => 'nirvix' . '/' . '0' . $product->id,
                         ];
                         if (isset($option['image']) && $request->hasFile("options.$index.image")) {
                             $variantImage = $request->file("options.$index.image");
                             $variantFileName = time() . '_' . $variantImage->getClientOriginalName();
                             $variantPath = $variantImage->move(public_path('uploads/variants'), $variantFileName);
                             $variantData['image'] = 'uploads/variants/' . $variantFileName;
                         }
                         $variant->update($variantData);
                     } else {
                         $newVariantData = [
                             'type' => $option['type'],
                             'name' => $option['name'],
                             'short_description' => $option['short_description'] ?? null,
                             'image' => null,
                         ];
                         if (isset($option['image']) && $request->hasFile("options.$index.image")) {
                             $variantImage = $request->file("options.$index.image");
                             $variantFileName = time() . '_' . $variantImage->getClientOriginalName();
                             $variantPath = $variantImage->move(public_path('uploads/variants'), $variantFileName);
                             $newVariantData['image'] = 'uploads/variants/' . $variantFileName;
                         }
     
                         $product->variants()->create($newVariantData);
                     }
                 }
                 $submittedVariantIds = array_column($request->options, 'id');
                 $variantsToDelete = array_diff($existingVariantIds, $submittedVariantIds);
                 $product->variants()->whereIn('id', $variantsToDelete)->delete();
             }
     
             return redirect()->route('products.index')->with('success', 'Product updated successfully.');
         } catch (\Exception $e) {
              \Log::error('Error updating product: ' . $e->getMessage());

            // \Log::info('Request data:', $request->all());
     
             return redirect()->back()->withErrors('An error occurred while updating the product. Please try again.');
         }
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
