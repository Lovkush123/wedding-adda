<?php

namespace App\Http\Controllers;

use App\Models\Pricing;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Pricing::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'price_name' => 'required|string',
            'price_type' => 'required|string',
            'price_category' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        $pricing = Pricing::create($request->all());

        return response()->json($pricing, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pricing = Pricing::findOrFail($id);
        return response()->json($pricing);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pricing = Pricing::findOrFail($id);

        $request->validate([
            'price_name' => 'string',
            'price_type' => 'string',
            'price_category' => 'string',
            'price' => 'numeric|min:0',
        ]);

        $pricing->update($request->all());

        return response()->json($pricing);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Pricing::findOrFail($id)->delete();
        return response()->json(['message' => 'Pricing deleted successfully']);
    }
}
