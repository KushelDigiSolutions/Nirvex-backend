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
            return response()->json([
                'isSuccess' => false,
                'errors' => [
                    'message' => 'No Sub Category found.',
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
