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
        $services = Service::orderBy('id', 'desc')->get();
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
     
     
         return redirect()->to('/admin/services')->with('success', 'Services updated successfully.');
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
         $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:0,1', 
            'type' => 'required|in:1,2',
            'image' => 'nullable|array', 
            'image.*' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
         ]);
        
         try {
            $service = Service::findOrFail($id);
           
           if(empty($request->delete_images)){
                $existingImages = explode(',', $service->image);
                foreach ($existingImages as $image) {
                    if (!empty($image) && file_exists(public_path($image))) {
                        unlink(public_path($image));
                    }
                }
                $service->image = null;
            }else if($request->has('delete_images') && is_array($request->delete_images)) {
                $existingImages = explode(',', $service->image); 
                $imagesToKeep = $request->delete_images; 
                $imagesToDelete = array_diff($existingImages, $imagesToKeep);
                foreach ($imagesToDelete as $image) {
                    if (file_exists(public_path($image))) {
                        unlink(public_path($image));
                    }
                }
                $service->image = implode(',', $imagesToKeep);
            } 
            if ($request->hasFile('image')) {
                $newImagePaths = [];
                foreach ($request->file('image') as $image) {
                    $fileName = time() . '_' . $image->getClientOriginalName();
                    $path = $image->move(public_path('uploads/products'), $fileName);
                    $newImagePaths[] = 'uploads/products/' . $fileName;
                }
                if (!empty($service->image)) {
                    $existingImages = explode(',', $service->image); 
                    $newImagePaths = array_merge($existingImages, $newImagePaths); 
                }
                $service->image = implode(',', $newImagePaths);
            }
             $service->name = $validatedData['name'];
             $service->type = $validatedData['type'];
             $service->description = $validatedData['description'];
             $service->status = (bool) $validatedData['status'];
             
             if (isset($newImagePaths)) {
                 $service->image = implode(',', array_filter($newImagePaths));
             }
             if (!$service->save()) {
                 throw new \Exception('Failed to save the Services.');
             }
             return redirect()->to('/admin/services')->with('success', 'Services updated successfully.');
            //  return redirect()->route('admin.services.index')->with('success', 'Services updated successfully.');
         } catch (\Exception $e) {
             \Log::error('Error updating Services: '.$e->getMessage());
            return redirect()
                  ->back()
                  ->withErrors(['message'=>__("Try again later. Error Details".$e)]);
         }
     }


    public function update02032025(Request $request, string $id)
    {
        \Log::info('Update method triggered');
        \Log::info($request->all());
        
        $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|in:0,1',
                'type' => 'required|in:1,2',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
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
