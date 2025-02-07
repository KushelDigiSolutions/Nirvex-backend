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
    if (!$categories) {
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => 'No categories found.',
            ],
            'data' =>[],
        ], 401); 

    }

    return response()->json([
        'isSuccess' => true,
        'errors' => [
            'message' => 'Categories retrieved successfully.',
        ],
        'data' =>$categories,
    ], 200);
}

   public function show($id)
    {
    // $category = Category::find($id);

    $category = DB::table('categories')->find($id);

    if (!$category) {
        
        return response()->json([
            'isSuccess' => false,
            'errors' => [
                'message' => 'Category not found.',
            ],
            'data' =>[],
        ], 401);
    
    }

    return response()->json([
        'isSuccess' => true,
        'errors' => [
            'message' => 'Categories retrieved successfully.',
        ],
        'data' =>$category,
    ], 200);
    // return response()->json($category, 200);
    }
}
