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

    public function index()
    {
        $services = Service::all();
        return view('admin.services.index', compact('services'));
    }


    public function create()
    {
        return view('admin.services.create');
    }

public function store(Request $request)
     {
         $validatedData = $request->validate([
             'name' => 'required|string|max:255',
             'description' => 'required|string',
             'status' => 'required|boolean',
             'image.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
             'type'    => 'required',
         ]);

         $imagePaths = [];
         if ($request->hasFile('image')) {
             foreach ($request->file('image') as $image) {
                 $fileName = time() . '_' . $image->getClientOriginalName(); 
                 $path = $image->move(public_path('uploads/services'), $fileName); 
                 $imagePaths[] = 'uploads/services/' . $fileName; 
             }
         }
         $validatedData['image'] = implode(',', $imagePaths);
         $service = Service::create($validatedData);
     
     
         return redirect()->route('services.create')->with('success', 'Services created successfully.');
     }


    public function show(string $id)
    {
        //
    }

   
    public function edit(string $id)
    {
        $serviceId = decrypt($id); 
        $services = Service::findOrFail($serviceId); 

        if ($services && $services->image) {
                $services->image = explode(',', $services->image);
        }
    
        return view('admin.services.edit', compact('services'));
    }

    public function update(Request $request, string $id)
    {
        \Log::info('Update method triggered');
        \Log::info($request->all());
        
        $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|boolean',
                'image.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
                'type'    => 'required|boolean',
         ]);

         try {
             $service = Service::findOrFail($id);
             $imagePaths = [];
             if ($request->hasFile('image')) {
                 foreach ($request->file('image') as $image) {
                     $fileName = time() . '_' . $image->getClientOriginalName();
                     $path = $image->move(public_path('uploads/services'), $fileName);
                     $imagePaths[] = 'uploads/services/' . $fileName;
                 }
                 $validatedData['image'] = implode(',', $imagePaths);
             }
             $service->update($validatedData);
     
             return redirect()->route('services.index')->with('success', 'Services updated successfully.');
         } catch (\Exception $e) {
              \Log::error('Error updating service: ' . $e->getMessage());

            // \Log::info('Request data:', $request->all());
     
             return redirect()->back()->withErrors('An error occurred while updating the service. Please try again.');
         }
    }

    public function destroy(string $id)
    {
        Service::where('id',decrypt($id))->delete();
        return redirect()->back()->with('success','Service deleted successfully.');
    }


    public function deleteImage(Request $request)
{
    $imagePath = $request->input('image_path');

    if (file_exists(public_path($imagePath))) {
        unlink(public_path($imagePath));
    }

    $service = Service::find($request->input('service_id')); 
    if ($service) {
        $images = explode(',', $service->image);
        $updatedImages = array_filter($images, fn($img) => $img !== $imagePath);
        $service->image = implode(',', $updatedImages); 
        $service->save();
    }

    return back()->with('success', 'Image deleted successfully.');
}


}
