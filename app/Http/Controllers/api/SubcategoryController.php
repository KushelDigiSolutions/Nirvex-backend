<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;

class SubcategoryController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve 'limit', 'sort', and 'category_id' query parameters from the request
        $limit = $request->query('limit', 10); // Default limit is 10 if not provided
        $sort = $request->query('sort', 'desc'); // Default sorting is descending
        $categoryId = $request->query('cat_id'); // Optional category filter

        // Validate the 'sort' parameter
        if (!in_array($sort, ['asc', 'desc'])) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'Invalid sort parameter. Only "asc" or "desc" are allowed.',
                ],
                'data' => [],
            ], 400);
        }

        // Build the query for subcategories
        $query = SubCategory::with('category')
            ->orderBy('id', $sort); // Sort by 'id' column (change as needed)
        // Apply category filter if 'category_id' is provided
        if ($categoryId) {
            $query->where('cat_id', $categoryId);
        }

        // Apply limit
        $subcategories = $query->limit($limit)->get();

        // Check if subcategories exist
        if ($subcategories->isEmpty()) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'No Sub Category found.',
                ],
                'data' => [],
            ], 404);
        }

        // Return success response with subcategories
        return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Sub Categories retrieved successfully.',
            ],
            'data' => $subcategories,
        ], 200);
    }



    
    public function show($id)
    { 
        // $SubCategory = SubCategory::findOrFail($id);  
        $subcategories = SubCategory::with('category')->find($id);
    
         if (!$subcategories) {
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => ' Sub Categories not found.',
                ],
                'data' =>[],
            ], 401);
         }
         
         return response()->json([
            'isSuccess' => true,
            'errors' => [
                'message' => 'Sub Categories retrieved successfully.',
            ],
            'data' =>$subcategories,
        ], 200);

    }
}
