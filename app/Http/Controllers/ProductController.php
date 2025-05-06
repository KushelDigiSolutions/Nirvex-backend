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


    public function updateToday06052025(Request $request, $id)
    {
        $product = Product::findOrFail($id);
    
        try {
            $validatedData = $request->validate([
                'pname' => 'required|string|max:255',
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
                'options.*.type' => 'nullable|string',
                'options.*.name' => 'required|string|max:255',
                'options.*.short_description' => 'nullable|string|max:255',
                'options.*.sku' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        $index = explode('.', $attribute)[1];
                        $variantId = $request->input("options.$index.id");
    
                        $exists = \DB::table('variants')
                            ->where('sku', $value)
                            ->when($variantId, function ($query) use ($variantId) {
                                return $query->where('id', '!=', $variantId);
                            })
                            ->exists();
    
                        if ($exists) {
                            $fail('The SKU has already been taken.');
                        }
                    },
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    
        // Image deletion
        if (empty($request->delete_images)) {
            $existingImages = explode(',', $product->image);
            foreach ($existingImages as $image) {
                if (!empty($image) && file_exists(public_path($image))) {
                    unlink(public_path($image));
                }
            }
            $product->image = null;
        } elseif (is_array($request->delete_images)) {
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
    
        // Image upload
        $imagePaths = [];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                if ($image->getSize() > 1024 * 1024) {
                    return redirect()->back()->withErrors(['image' => 'Each image must not be greater than 1MB.']);
                }
    
                $fileName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/products'), $fileName);
                $imagePaths[] = 'uploads/products/' . $fileName;
            }
        }
    
        if (!empty($imagePaths)) {
            $validatedData['image'] = implode(',', $imagePaths);
        } else {
            unset($validatedData['image']);
        }
    
        // Update product
        $product->update([
            'name' => $validatedData['pname'],
            'description' => $validatedData['descriptions'],
            'cat_id' => $validatedData['cat_id'],
            'sub_cat_id' => $validatedData['sub_cat_id'],
            'mrp' => $validatedData['mrp'],
            'availability' => $validatedData['availability'],
            'return_policy' => $validatedData['return_policy'],
            'specification' => $validatedData['specification'],
            'physical_property' => $validatedData['physical_property'],
            'standards' => $validatedData['standards'],
            'key_benefits' => $validatedData['key_benefits'],
            'status' => $validatedData['status'],
            'image' => $validatedData['image'] ?? $product->image,
        ]);
    
        // Update variants
        if ($request->has('options')) {
            foreach ($request->options as $option) {
                $variantData = [
                    'product_id' => $product->id,
                    'name' => $option['name'],
                    'type' => $option['type'] ?? null,
                    'sku' => $option['sku'],
                    'short_description' => $option['short_description'] ?? null,
                ];
    
                // Upload variant image if exists
                if (isset($option['image']) && $option['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $fileName = time() . '_' . $option['image']->getClientOriginalName();
                    $option['image']->move(public_path('uploads/variants'), $fileName);
                    $variantData['images'] = 'uploads/variants/' . $fileName;
                }
    
                if (!empty($option['id'])) {
                    // Update
                    Variant::where('id', $option['id'])->update($variantData);
                } else {
                    // Create
                    Variant::create($variantData);
                }
            }
        }
    
        session()->flash('success', 'Product updated successfully.');
        return redirect()->route('products.index');
    }
    


    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
    
        try {
            $validatedData = $request->validate([
                'pname' => 'required|string|max:255',
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
                'options.*.type' => 'nullable|string',
                'options.*.name' => 'required|string|max:255',
                'options.*.short_description' => 'nullable|string|max:255',
               'options.*.sku' => [
    'required',
    function ($attribute, $value, $fail) use ($request) {
        $index = explode('.', $attribute)[1]; 
        $variantId = $request->input("options.$index.id"); 

        if ($variantId) {
            $exists = \DB::table('variants')
                ->where('sku', $value)
                ->where('id', '!=', $variantId) 
                ->exists();

            if ($exists) {
                $fail('The SKU has already been taken.');
            }
        }
    },
],
]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                if ($file->getSize() > 1024 * 1024) { 
                    return redirect()->back()->withErrors(['image' => 'Each image must not be greater than 1MB.']);
                }
            }
        }

        if (empty($request->delete_images)) {
            $existingImages = explode(',', $product->image);
            foreach ($existingImages as $image) {
                if (!empty($image) && file_exists(public_path($image))) {
                    unlink(public_path($image));
                }
            }
            $product->image = null;
        } elseif ($request->has('delete_images') && is_array($request->delete_images)) {
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
    
        $imagePaths = [];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $fileName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/products'), $fileName);
                $imagePaths[] = 'uploads/products/' . $fileName;
            }
        }
    
        if (!empty($imagePaths)) {
            $validatedData['image'] = implode(',', $imagePaths);
        } else {
            unset($validatedData['image']);
        }
    
        $product->update([
            'name' => $validatedData['pname'],
            'description' => $validatedData['descriptions'],
            'cat_id' => $validatedData['cat_id'],
            'sub_cat_id' => $validatedData['sub_cat_id'],
            'mrp' => $validatedData['mrp'],
            'availability' => $validatedData['availability'],
            'return_policy' => $validatedData['return_policy'],
            'specification' => $validatedData['specification'],
            'physical_property' => $validatedData['physical_property'],
            'standards' => $validatedData['standards'],
            'key_benefits' => $validatedData['key_benefits'],
            'status' => $validatedData['status'],
        ]);
        if ($request->has('options')) {
            foreach ($request->options as $option) {
        
                // Build data array
                $variantData = [
                    'product_id' => $product->id,
                    'name' => $option['name'],
                    'type' => $option['type'] ?? null,
                    'sku' => $option['sku'] ?? null,
                    'short_description' => $option['short_description'] ?? null,
                ];
        
                // Handle image upload
                if (isset($option['image']) && $option['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $fileName = time() . '_' . $option['image']->getClientOriginalName();
                    $option['image']->move(public_path('uploads/variants'), $fileName);
                    $variantData['images'] = 'uploads/variants/' . $fileName;
                }
        
                // Check if SKU exists for same product and update that record
                $existingVariant = Variant::where('sku', $option['sku'])
                    ->where('product_id', $product->id)
                    ->first();
        
                if ($existingVariant) {
                    // Update existing variant
                    $existingVariant->update($variantData);
                } else {
                    // Create new variant
                    Variant::create($variantData);
                }
            }
        }
        
        
        session()->flash('success', 'Product updated successfully.');
        return redirect()->route('products.index');

        //  return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }


    public function update030425(Request $request, $id)
{
    // Find the product
    $product = Product::findOrFail($id);

    try {
        $validatedData = $request->validate([
            'pname' => 'required|string|max:255',
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
            'options.*.type' => 'nullable|string',
            'options.*.name' => 'required|string|max:255',
            'options.*.short_description' => 'nullable|string|max:255',
            'options.*.sku' => [
                'nullable', 
                Rule::unique('variants', 'sku')->ignore($request->options['id'] ?? null)
            ],
            'options.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()->withErrors($e->errors());
    }

    if ($request->hasFile('image')) {
        foreach ($request->file('image') as $file) {
            if ($file->getSize() > 1024 * 1024) { 
                return redirect()->back()->withErrors(['image' => 'Each image must not be greater than 1MB.']);
            }
        }
    }

    if(empty($request->delete_images)){
        $existingImages = explode(',', $product->image);
        foreach ($existingImages as $image) {
            if (!empty($image) && file_exists(public_path($image))) {
                unlink(public_path($image));
            }
        }
        $product->image = null;
    } elseif ($request->has('delete_images') && is_array($request->delete_images)) {
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

    // Update product data
    $product->update([
        'name' => $validatedData['pname'],
        'description' => $validatedData['descriptions'],
        'cat_id' => $validatedData['cat_id'],
        'sub_cat_id' => $validatedData['sub_cat_id'],
        'mrp' => $validatedData['mrp'],
        'availability' => $validatedData['availability'],
        'return_policy' => $validatedData['return_policy'],
        'specification' => $validatedData['specification'],
        'physical_property' => $validatedData['physical_property'],
        'standards' => $validatedData['standards'],
        'key_benefits' => $validatedData['key_benefits'],
        'status' => $validatedData['status'],
    ]);

    if ($request->has('options')) {
        foreach ($request->options as $option) {
            $variantData = [
                'product_id' => $product->id,
                'name' => $option['name'],
                'type' => $option['type'] ?? null,
                'sku' => $option['sku'] ?? null,
                'short_description' => $option['short_description'] ?? null,
            ];

            if (isset($option['image']) && $option['image'] instanceof \Illuminate\Http\UploadedFile) {
                $fileName = time() . '_' . $option['image']->getClientOriginalName();
                $path = $option['image']->move(public_path('uploads/variants'), $fileName);
                $variantData['images'] = 'uploads/variants/' . $fileName;
            }

            if (!empty($option['id'])) {
                // Update existing variant
                Variant::where('id', $option['id'])->update($variantData);
            } else {
                // Create new variant
                Variant::create($variantData);
            }

            // Variant::update(
            //     ['id' => $option['id'] ?? null, 'product_id' => $product->id],
            //     $variantData
            // );
        }
    }

    return redirect()->route('products.index', ['product' => $product->id])->with('success', 'Product updated successfully.');
}


    public function update210325(Request $request, $id)
    {
        // Find the product
        $product = Product::findOrFail($id);
        try {
            $validatedData = $request->validate([
                'pname' => 'required|string|max:255',
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
                'options' => 'required|array',
                'options.*.id' => 'nullable|integer', 
                'options.*.type' => 'nullable|string',
                'options.*.name' => 'required|string|max:255',
                'options.*.short_description' => 'nullable|string|max:255',
                'options.*.sku' => function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1]; 
                    $variantId = $request->options[$index]['id'] ?? null;
                    $rule = Rule::unique('variants', 'sku');
                    if ($variantId) {
                        $rule->ignore($variantId);
                    }
                    $validator = validator([$attribute => $value], [$attribute => $rule]);
                    if ($validator->fails()) {
                        $fail($validator->errors()->first($attribute));
                    }
                },
                'options.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            ]);

            // dd($validatedData);
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            // dd($e->errors()); 
        }
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                if ($file->getSize() > 1024 * 1024) { 
                    return redirect()->back()->withErrors(['image' => 'Each image must not be greater than 1MB.']);
                }
            }
        }

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
            $imagePaths = [];
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

        // $product->update($validatedData);
        $product->name = $validatedData['pname'];
        $product->description = $validatedData['descriptions'];
        $product->cat_id = $validatedData['cat_id'];
        $product->sub_cat_id  = $validatedData['sub_cat_id'];
        $product->mrp = $validatedData['mrp'];
        $product->availability = $validatedData['availability'];
        $product->return_policy = $validatedData['return_policy'];
        $product->specification = $validatedData['specification']; 
        $product->physical_property = $validatedData['physical_property'];
        $product->standards = $validatedData['standards'];
        $product->key_benefits = $validatedData['key_benefits'];
        $product->status = $validatedData['status'];

        if ($request->has('options')) {
            dd($request->options);
            foreach ($request->options as $option) {
                if (isset($option['id'])) {
                    $variant = Variant::findOrFail($option['id']);
                    $variantData = [
                        'name' => $option['name'],
                        'type' => $option['type'],
                        'sku' => $option['sku'],
                        'short_description' => $option['short_description'] ?? null,
                    ];
                    dd($variantData);
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
