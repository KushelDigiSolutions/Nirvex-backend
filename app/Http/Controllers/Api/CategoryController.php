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
    $categories = DB::table('categories')->get();
    if ($categories->isEmpty()) {
        return response()->json(['message' => 'No categories found.'], 404);
    }

    return response()->json([
        'message' => 'Categories retrieved successfully.',
        'data' => $categories,
    ], 200);
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



}
