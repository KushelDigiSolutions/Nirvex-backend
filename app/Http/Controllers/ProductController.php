<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
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
        $products = Product::with(['category', 'subCategory'])->get();
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
    
        // dd($request);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'cat_id' => 'required|integer',
            'sub_cat_id' => 'required|integer',
            'status' => 'required|boolean',
            'mrp' => 'required|numeric',
            'availability' => 'required|string',
            'specification' => 'required|string',
            'image.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $imagePaths = [];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $fileName = time() . '_' . $image->getClientOriginalName(); 
                $path = $image->move(public_path('uploads/products'), $fileName); 
                $imagePaths[] = 'uploads/products/' . $fileName; 
            }
        }
    
        $validatedData['image'] = implode(',', $imagePaths);    
        $product = Product::create($validatedData);

        return redirect()->route('products.create')->with('success', 'Products created successfully.');

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
        $products = Product::with(['category', 'subCategory'])->find(decrypt($id));
        if ($products && $products->image) {
            $products->image = explode(',', $products->image);
        }
        return view('admin.products.edit',compact('categories','subCategories','products'));
    }

    /**
     * Update the specified resource in storage.
     */


     public function update(Request $request, string $id)
     {
        echo $id; die;
         $validatedData = $request->validate([
             'name' => 'required|string|max:255',
             'description' => 'required|string',
             'cat_id' => 'required|integer|exists:categories,id', 
             'sub_cat_id' => 'required|integer|exists:sub_categories,id', 
             'status' => 'required|boolean',
             'mrp' => 'required|numeric|min:0',
             'availability' => 'required|string',
             'specification' => 'required|string',
             'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
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
                 $validatedData['image'] = implode(',', $imagePaths);
             }
             $product->update($validatedData);
             return redirect()->route('products.index')->with('success', 'Product updated successfully.');
         } catch (\Exception $e) {
             \Log::error('Error updating product: ' . $e->getMessage());
     
             return redirect()->back()->withErrors('An error occurred while updating the product. Please try again.');
         }
     }
     

    public function update06012024(Request $request, string $id)
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
            'image.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $imagePaths = [];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $fileName = time() . '_' . $image->getClientOriginalName(); 
                $path = $image->move(public_path('uploads/products'), $fileName); 
                $imagePaths[] = 'uploads/products/' . $fileName; 
            }
        }
        $validatedData['image'] = implode(',', $imagePaths);    
        $product = Product::update($validatedData);
        return redirect()->route('products.index')->with('success', 'Products updated successfully.');
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
}
