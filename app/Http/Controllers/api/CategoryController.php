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
    public function index(Request $request)
    {
        // Retrieve 'limit' and 'sort' query parameters from the request
        $limit = $request->query('limit', 10); // Default limit is 10 if not provided
        $sort = $request->query('sort', 'asc'); // Default sorting is ascending

        // Validate the query parameters
        if (!in_array($sort, ['asc', 'desc'])) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Invalid sort parameter. Only "asc" or "desc" are allowed.',
                ],
                'data' => [],
            ], 400);
        }

        // Fetch categories from the database with sorting and limiting
        $categories = DB::table('categories')
            ->orderBy('name', $sort) // Sort by 'name' column (change as per your column)
            ->limit($limit)
            ->get();

        // Check if categories exist
        if ($categories->isEmpty()) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'No categories found.',
                ],
                'data' => [],
            ], 404);
        }

        // Return success response with categories
        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Categories retrieved successfully.',
            ],
            'data' => $categories,
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
