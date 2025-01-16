<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'SubCategory'])->get();
        
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No Sub Category found.'], 404);
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
        $products = Product::with(['category', 'SubCategory'])->find($id);
        if ($products && $products->image) {
            $products->image = explode(',', $products->image);
        }
        return response()->json([
            'message' =>  'Product retrieved successfully',
            'products' => $products,
            'categories' => $categories,
            'subCategories' => $subCategories,
            ], 200);
    }
}
