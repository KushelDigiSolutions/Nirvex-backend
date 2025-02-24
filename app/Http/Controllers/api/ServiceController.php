<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
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
          if (!$services) {
            return response()->json(['isSuccess' =>false,
            'error' => ['message' => 'Services not found.'],
            'data' => [],], 401);
        }

    return response()->json(['isSuccess' =>true,
        'error' => ['message' => 'Services retreived successfully.'],
        'data' => $services,
    ], 200);
    }

   
    public function show(string $id)
    {
        // Find the service by ID
        $services = Service::find($id);
    
        // If the service is not found
        if (!$services) {
            // Check if the request expects JSON (API request)
            if (request()->expectsJson()) {
                return response()->json([
                    'isSuccess' => false,
                    'error' => ['message' => 'Services not found.'],
                    'data' => [],
                ], 401);
            }
    
            // For non-API requests, return a 404 view or redirect
            abort(404, 'Service not found.');
        }
    
        // If it's an API request, return JSON response
        if (request()->expectsJson()) {
            // Process the image field to convert it into an array
            $images = explode(',', $services->image); // Assuming images are stored as a comma-separated string
    
            // Prepare the response data with images as an array
            $responseData = [
                'id' => $services->id,
                'name' => $services->name,
                'type' => $services->type,
                'description' => $services->description,
                'images' => $images, // Images as an array
                'status' => $services->status,
            ];
    
            // Return the success response with the processed data
            return response()->json([
                'isSuccess' => true,
                'error' => ['message' => 'Services retrieved successfully.'],
                'data' => $responseData,
            ], 200);
        }
    
        // For non-API requests, return the HTML page (e.g., Blade view)
        return view('services.show', compact('services'));
    }
    


    public function getServices(Request $request)
    {
        // Validate the request parameters
        $request->validate([
            'type' => 'nullable|integer', // Ensure 'type' is numeric
            'limit' => 'nullable|integer|min:1', // Validate limit as an integer greater than 0
            'sort' => 'nullable|string|in:asc,desc', // Validate sort direction
        ]);
    
        // Retrieve query parameters
        $type = $request->query('type');
        $limit = $request->query('limit', 10); // Default limit is 10
        $sort = $request->query('sort', 'asc'); // Default sorting is ascending
    
        // Build the query
        $query = Service::query();
    
        // Filter by type if provided
        if (!is_null($type)) {
            $query->where('type', $type);
        }
    
        // Apply sorting and limit
        $services = $query->orderBy('name', $sort) // Sort alphabetically by name
                          ->limit($limit) // Limit the number of results
                          ->get();
    
        // Process services to format the image field as an array and apply the limit rule for images
        $processedServices = $services->map(function ($service) use ($limit) {
            // Convert image field to an array (assuming images are stored as a comma-separated string)
            $images = explode(',', $service->image); 
    
            // If limit <= 6, include only the first image in the array
            if ($limit <= 6) {
                $images = array_slice($images, 0, 1);
            }
    
            // Return the service with modified images array
            return [
                'id' => $service->id,
                'name' => $service->name,
                'type' => $service->type,
                'description' => $service->description,
                'images' => $images, // Images as an array
                'status' => $service->status,
            ];
        });
    
        // Return the response
        return response()->json([
            'isSuccess' => true,
            'error' => ['message' => 'Services retrieved successfully.'],
            'data' => $processedServices,
        ], 200);
    }
    



}
