<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'SubCategory', 'variants'])->get();
        
        if ($products->isEmpty()) {
            return response()->json(['isSuccess'=>false,
                'error' => ['message' => 'No Product found.'],
                'data' =>[],
            ], 401);
        }

        return response()->json([
            'message' =>  'Product retrieved successfully',
            'data' => $products,
            ], 200);
    }

    public function show(string $id)
    {
        $categories = Category::all();
        $subCategories = SubCategory::all();
        $products = Product::with(['category', 'SubCategory', 'variants'])->find($id);
        if(!$products){
            return response()->json(['isSuccess' =>false,
            'error' => ['message' =>  'Product not found'],
            'data' => [],
            ], 401);
        }
        if ($products && $products->image) {
            $products->image = explode(',', $products->image);
        }
        return response()->json(['isSuccess' =>true,
            'error' => ['message' =>  'Product retrieved successfully'],
            'data' => $products,
            ], 200);
    }
}
