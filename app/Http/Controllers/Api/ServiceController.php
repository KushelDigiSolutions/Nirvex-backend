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
          if ($services->isEmpty()) {
        return response()->json(['message' => 'No Service found.'], 404);
    }

    return response()->json([
        'message' => 'Service retrieved successfully.',
        'data' => $services,
    ], 200);
    }

   
    public function show(string $id)
    {
        // $serviceId = decrypt($id); 
        $services = Service::findOrFail($id); 
         if (!$services) {
        return response()->json(['message' => 'Category not found.'], 404);
    }

     return response()->json([
        'message' => 'Service retrieved successfully.',
        'data' => $services,
    ], 200);
   
    }

}
