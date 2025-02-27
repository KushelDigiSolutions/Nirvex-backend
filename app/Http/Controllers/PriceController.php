<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Pricing;
use App\Models\Product;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        public function index()
        {
            $pricings = Pricing::all();
            return view('admin.pricings.index', compact('pricings'));
        }

     

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::orderBy('id', 'desc')->get();
        return view('admin.pricings.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       
        $validatedData = $request->validate([
            'product_id' => 'required|integer', 
            'product_sku_id' => 'required|string',
            'pincode' => 'required|digits:6',
            'mrp' => 'required|numeric',
            'price' => 'required|numeric',
            'tax_value' => 'required|numeric',
            'ship_charges' => 'required|numeric',
            'valid_upto' => 'required|date_format:Y-m-d\TH:i',
            'status' => 'required|boolean',
            'is_cash' => 'required|boolean',
        
        ], 
        [
            'pin_code.digits' => 'The Pin Code must be exactly 6 digits.',
            'mrp.numeric' => 'The MRP must be a valid numeric value.',
            'price.numeric' => 'The Sale Price must be a valid numeric value.',
            'tax_value.numeric' => 'The Tax Value must be a valid numeric value.',
            'ship_charges.numeric' => 'Shipping charges must be a valid numeric value.',
            'valid_upto.date' => 'The Valid Upto field must be a valid date.',
            'status.boolean' => 'The Status field must be true or false.',
            'is_cash.boolean' => 'The Cash Payment field must be true or false.',
        ]);
        // dd($request->all());
        Pricing::create($validatedData);
        return redirect()->route('pricings.index')->with('success', 'Pricing created successfully.');
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    // }

    public function show(Pricing $pricing)
    {
        return view('pricings.show', compact('pricing'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pricing = DB::table('pricings')->where('id', decrypt($id))->first();
        return view('admin.pricings.edit', compact('pricing'));
    }

    /**
     * Update the specified resource in storage.
     */


public function update(Request $request, string $id)
{
    // dd($id);
    $request->validate([
        'pincode' => 'required|string',
        'product_id' => 'required|integer',
        'product_sku_id' => 'required|string',
        'mrp' => 'required|numeric',
        'price' => 'required|numeric',
        'tax_type' => 'required|in:0,1',
        'tax_value' => 'required|numeric',
        'ship_charges' => 'required|numeric',
        'valid_upto' => 'required|date',
        'status' => 'required|boolean',
    ]);

    
    $pricing = Pricing::find($id);

    if (!$pricing) {
        return redirect()->back()->with('error', 'Pricing not found.');
    }
    // dd($pricing);
    // Update fields
    DB::table('pricings')->where('id', $id)->update([
        'pincode' => $request->pincode,
        'product_id' => $request->product_id,
        'product_sku_id' => $request->product_sku_id,
        'mrp' => $request->mrp,
        'price' => $request->price,
        'tax_type' => $request->tax_type,
        'tax_value' => $request->tax_value,
        'ship_charges' => $request->ship_charges,
        'valid_upto' => $request->valid_upto,
        'status' => $request->status,
        'is_cash' => $request->is_cash,
        'updated_at' => now(), // Manually update timestamp
    ]);
    return redirect()->route('pricings.index')->with('success', 'Pricing updated successfully.');
}


public function destroy(string $id)
{
    $pricing = DB::table('pricings')->where('id', decrypt($id))->delete();
    return redirect()->back()->with('success','Email deleted successfully.');
}

    // public function destroy(Price $pricing)
    // {
    //     $pricing->delete();
    //     return redirect()->route('pricings.index')->with('success', 'Pricing deleted successfully.');
    // }
    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    // }
}
