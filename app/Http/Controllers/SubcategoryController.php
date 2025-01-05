<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Facades\DB;

class SubcategoryController extends Controller
{
    public function index()
    {
        $subcategories = Subcategory::with('category')->orderby('id','desc')->get();
        
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
            'cat_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'status' => ['required'],
            'image' => ['required', 'file', 'mimes:jpeg,png,jpg,gif', 'max:4096']
        ]);

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads/subcategories'), $imageName);

        // Subcategory::create($request->all());
        $blog = Subcategory::create([
            'cat_id' => $request->cat_id,
            'name' => $request->name,
            'status' => $request->status,
            'image' => 'uploads/subcategories/' . $imageName ?? null
        ]);
        return redirect()->route('subcategories.index')->with('success', 'Subcategory created successfully.');
    }

    public function edit($id)
    {
        $subcategoryId = decrypt($id); 
        $subcategory = Subcategory::findOrFail($subcategoryId); 

        // $categories = Category::all(); 
        $categories = Subcategory::with('category')->get();
    return view('admin.subcategories.edit', compact('subcategory', 'categories'));
    }


    // public function edit(Subcategory $subcategory)
    // {
    //     dd($subcategory);
    //     // $categories = Category::all();
    //     $catid = decrypt($id);
    //     echo $catid; die;
    //     $category = Category::where('id', $catid)->firstOrFail();
    //     return view('subcategories.edit', compact('subcategory', 'categories'));
    // }

    // public function update(Request $request, Subcategory $subcategory, $id)
    public function update(Request $request, string $id)
    {
        
        $request->validate([
            'cat_id' => ['required'],
            'name' => 'required|string|max:255',
            'status' => ['required'],
            'image' => ['required', 'file', 'mimes:jpeg,png,jpg,gif', 'max:4096']
        ]); 
        
        $subcategories = DB::table('subcategories')->where('id', $id)->first();
        if ($request->hasFile('image')) {
            if ($subcategories->image && file_exists(public_path($subcategories->image))) {
                unlink(public_path($subcategories->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/subcategories'), $imageName);
            $subcategories->image = 'uploads/subcategories/' . $imageName;
        }
        DB::table('subcategories')
        ->where('id', $id) 
        ->update([
            'cat_id' => $request->cat_id,
            'name' => $request->name,
            'status' => $request->status,
            'image' => isset($imageName) ? 'uploads/subcategories/' . $imageName : null,
        ]);
        return redirect()->route('subcategories.index')->with('success', 'Subcategory updated successfully.');
    }

    public function destroy(string $id)
    {
        $categories = DB::table('categories')->where('id', decrypt($id))->delete();
        return redirect()->back()->with('success','Email deleted successfully.');
    }

    public function destroy0312024(Subcategory $subcategory)
    {
        $subcategory->delete();
        return redirect()->route('subcategories.index')->with('success', 'Subcategory deleted successfully.');
    }
}
