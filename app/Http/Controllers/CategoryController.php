<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class CategoryController extends Controller
{
  
    public function index()
    {
        $category = DB::table('categories')->orderBy('id','desc')->get();
        return view('admin.categories.index', compact('category'));
    }

  
    public function create()
    {
        return view('admin.categories.create');
    }

   
    public function store(Request $request)
    {

       $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required'],
            'image' => ['required', 'file', 'mimes:jpeg,png,jpg,gif', 'max:4096']
        ]);

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads/categories'), $imageName);

        $blog = Category::create([
            'name' => $request->name,
            'status' => $request->status,
            'image' => 'uploads/categories/' . $imageName ?? null
        ]);

        return redirect()->route('categories.index')->with('success', 'Categories created successfully.');
    }

    public function show(string $id)
    {
        //
    }

  
    public function edit(string $id)
    {
      
        $categories = DB::table('categories')->where('id', decrypt($id))->first();
        // echo '<pre>'; print_r($categories); die;
        return view('admin.categories.edit',compact('categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required'],
            'image' => ['required', 'file', 'mimes:jpeg,png,jpg,gif', 'max:4096']
        ]);

        $categories = DB::table('categories')->where('id', $id)->first();

        if ($request->hasFile('image')) {
            if ($categories->image && file_exists(public_path($categories->image))) {
                unlink(public_path($categories->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/categories'), $imageName);
            $categories->image = 'uploads/categories/' . $imageName;
        }
        DB::table('categories')
        ->where('id', $id) 
        ->update([
            'name' => $request->name,
            'status' => $request->status,
            'image' => isset($imageName) ? 'uploads/categories/' . $imageName : null,
        ]);

        // if ($request->hasFile('image')) {
        //     $categories->save();
        // }

        return redirect()->route('categories.index')->with('success', 'Categories updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categories = DB::table('categories')->where('id', decrypt($id))->delete();
        return redirect()->back()->with('success','Email deleted successfully.');
    }
}
