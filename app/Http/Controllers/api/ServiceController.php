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
    
    $request->validate([
        'type' => 'nullable|integer', 
    ]);

    $type = $request->query('type');
    $query = Service::query();
    if (!is_null($type)) {
        $query->where('type', $type);
    }

    $services = $query->get();
    return response()->json(['isSuccess' => true,
        'error' => ['message' => 'Services Retreived successfully'],
        'data' => $services
    ], 200);
}

}
