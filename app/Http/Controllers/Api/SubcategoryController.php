<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;

class SubcategoryController extends Controller
{
    public function index()
    {
        $subcategories = SubCategory::with('category')->orderby('id','desc')->get();
        if ($subcategories->isEmpty()) {
            return response()->json(['message' => 'No Sub Category found.'], 404);
        }
        return response()->json([
                            'message' =>  'Sub Category retrieved successfully',
                            'data' => $subcategories,
        ], 200);
    }

    
    public function show($id)
    {
        // $subcategoryId = decrypt($id); 
        $SubCategory = SubCategory::findOrFail($id);  
        $categories = SubCategory::with('category')->get();
    
        // if ($SubCategory->isEmpty()) {
        //     return response()->json(['message' => 'No Sub Category found.'], 404);
        // }
        return response()->json([
                            'message' =>  'Sub Category retrieved successfully',
                            'data' => $categories,
        ], 200);

    }


}
