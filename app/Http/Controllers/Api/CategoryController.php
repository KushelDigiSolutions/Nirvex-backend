<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // $categories = Category::all();
    $categories = DB::table('categories')->get();
    if ($categories->isEmpty()) {
        return response()->json(['message' => 'No categories found.'], 404);
    }

    return response()->json([
        'message' => 'Categories retrieved successfully.',
        'data' => $categories,
    ], 200);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->store('categories', 'public'); 
            $validated['image'] = 'uploads/categories/' . basename($filePath); 
        }

        $category = Category::create($validated);

        return response()->json(['message' => 'Category created successfully.', $category], 201);
    }

   public function show($id)
{
    // $category = Category::find($id);

    $category = DB::table('categories')->where('id',$id)->first();

    if (!$category) {
        return response()->json(['message' => 'Category not found.'], 404);
    }

    return response()->json($category, 200);
}


public function update(Request $request, $id)
{
    // $category = Category::find($id);
    $category = DB::table('categories')->where('id', $id)->first();
    if (!$category) {
        return response()->json(['message' => 'Category not found.'], 404);
    }
    dd($request);
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'active' => 'boolean',
    ]);


     

    if ($request->hasFile('image')) {
        $filePath = $request->file('image')->store('categories', 'public'); 
        $validated['image'] = 'uploads/categories/' . basename($filePath); 
    }
     $category->save($validated);

    // DB::table('categories')->where('id', $id)->update($validated);

    $updatedCategory = DB::table('categories')->where('id', $id)->first();
    return response()->json(['message' => 'Category updated successfully.', 'category' => $updatedCategory], 200);
}

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
