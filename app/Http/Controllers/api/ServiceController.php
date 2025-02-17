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
        // $serviceId = decrypt($id); 
        $services = Service::find($id); 
        if(!$services) {
        return response()->json(['isSuccess' =>false,
                    'error' => ['message' => 'Services not found.'],
                    'data' =>[],
         ], 401);
    }

     return response()->json(['isSuccess' =>true,
        'error' => ['message' => 'Services Retreived successfully.'],
        'data' => $services,
    ], 200);
   
    }

    public function getServices(Request $request)
    {
        // Validate the request parameters
        $request->validate([
            'type' => 'nullable|integer', 
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

        // Return the response
        return response()->json([
            'isSuccess' => true,
            'error' => ['message' => 'Services retrieved successfully'],
            'data' => $services,
        ], 200);
    }


}
