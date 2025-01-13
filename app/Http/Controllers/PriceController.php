<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.pricings.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pricings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            $request->validate([
                'search_product' => 'required|string|max:255',
                'variant_name' => 'required|string',
                'pin_code' => 'nullable|string|max:10',
                'sku' => 'nullable|string|max:50',
                'short_description' => 'nullable|string|max:500',
                'mrp' => 'required|numeric',
                'sale_price' => 'nullable|numeric',
                'tax_type' => 'required|string',
                'tax_value' => 'nullable|numeric',
                'valid_upto' => 'nullable|date',
            ]);
    
            $pricing = new Pricing();
            $pricing->product_name = $request->input('search_product');
            $pricing->variant_name = $request->input('variant_type');
            $pricing->pin_code = $request->input('pin_code');
            $pricing->sku = $request->input('sku');
            $pricing->short_description = $request->input('short_description');
            $pricing->mrp = $request->input('mrp');
            $pricing->sale_price = $request->input('sale_price');
            $pricing->tax_type = $request->input('tax_type');
            $pricing->tax_value = $request->input('tax_value');
            $pricing->valid_upto = $request->input('valid_upto');
            
            $pricing->save(); 
    
            return redirect()->route('pricings.index')->with('success', 'Pricing saved successfully.');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
