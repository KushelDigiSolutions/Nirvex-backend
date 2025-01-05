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

        //   dd($validatedData['images']);
       

        $product = Product::create($validatedData);

        return redirect()->route('products.create')->with('success', 'Products created successfully.');
        // return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);

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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
