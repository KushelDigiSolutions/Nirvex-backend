<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::all();
        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required'],
            'image' => ['required', 'file', 'mimes:jpeg,png,jpg,gif', 'max:4096']
        ]);

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads/services'), $imageName);

        $service = Service::create([
            'name' => $request->name,
            'status' => $request->status,
            'image' => 'uploads/services/' . $imageName ?? null
        ]);

        return redirect()->route('services.index')->with('success', 'Services created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $serviceId = decrypt($id); 
        $services = Service::findOrFail($serviceId); 
    
        return view('admin.services.edit', compact('services'));
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

        $services = DB::table('services')->where('id', $id)->first();

        if ($request->hasFile('image')) {
            if ($services->image && file_exists(public_path($services->image))) {
                unlink(public_path($services->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/services'), $imageName);
            $services->image = 'uploads/services/' . $imageName;
        }
        DB::table('services')
        ->where('id', $id) 
        ->update([
            'name' => $request->name,
            'status' => $request->status,
            'image' => isset($imageName) ? 'uploads/services/' . $imageName : null,
        ]);

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Service::where('id',decrypt($id))->delete();
        return redirect()->back()->with('success','Service deleted successfully.');
    }
}
