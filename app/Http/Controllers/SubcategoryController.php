<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;

class SubcategoryController extends Controller
{
    public function index()
    {
        $subcategories = SubCategory::with('category')->orderby('id','desc')->get();
        
        return view('admin.subcategories.index', compact('subcategories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.subcategories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => ['required'],
            'cat_id' => 'required|exists:categories,id',
            'image' => ['required', 'file', 'mimes:jpeg,png,jpg,gif', 'max:4096']
        ]);
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads/subcategories'), $imageName);
        
        $blog = SubCategory::create([
            'cat_id' => $request->cat_id,
            'name' => $request->name,
            'status' => $request->status,
            'image' => 'uploads/subcategories/' . $imageName ?? null
        ]);

        return redirect()->route('subcategories.index')->with('success', 'SubCategory created successfully.');
    }

    public function edit($id)
    {
        $subcategoryId = decrypt($id); 
        $SubCategory = SubCategory::findOrFail($subcategoryId); 
        $categories = Category::all(); 
        return view('admin.subcategories.edit', compact('SubCategory', 'categories'));
    }


    // public function edit(SubCategory $SubCategory)
    // {
    //     dd($SubCategory);
    //     // $categories = Category::all();
    //     $catid = decrypt($id);
    //     echo $catid; die;
    //     $category = Category::where('id', $catid)->firstOrFail();
    //     return view('subcategories.edit', compact('SubCategory', 'categories'));
    // }

    // public function update(Request $request, SubCategory $SubCategory, $id)
    public function update(Request $request, string $id)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'cat_id' => ['required'],
            'status' => ['required'],
            'image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif', 'max:4096']
        ]); 
        
        $subcategories = DB::table('subcategories')->where('id', $id)->first();
        $imagePath = $subcategories->image;
        if ($request->hasFile('image')) {
            if ($subcategories->image && file_exists(public_path($subcategories->image))) {
                unlink(public_path($subcategories->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/subcategories'), $imageName);
            $imagePath = 'uploads/subcategories/' . $imageName;
        }
        DB::table('subcategories')
        ->where('id', $id) 
        ->update([
            'cat_id' => $request->cat_id,
            'name' => $request->name,
            'status' => $request->status,
            'image' => $imagePath,
            // 'image' => isset($imageName) ? 'uploads/subcategories/' . $imageName : null,
        ]);
        return redirect()->route('subcategories.index')->with('success', 'SubCategory updated successfully.');
    }

    public function destroy(string $id)
    {
        $categories = DB::table('subcategories')->where('id', decrypt($id))->delete();
        return redirect()->back()->with('success','Email deleted successfully.');
    }

    public function destroy0312024(SubCategory $SubCategory)
    {
        $SubCategory->delete();
        return redirect()->route('subcategories.index')->with('success', 'SubCategory deleted successfully.');
    }
}
