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
            'vender_id' => 'required|integer',
            'price_name' => 'required|string',
            'price_type' => 'required|string',
            'price_category' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $pricing = Pricing::create($request->all());

        return response()->json($pricing, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pricing $pricing)
    {
        return response()->json($pricing);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pricing $pricing)
    {
        $request->validate([
            'vender_id' => 'integer',
            'price_name' => 'string',
            'price_type' => 'string',
            'price_category' => 'string',
            'price' => 'numeric',
        ]);

        $pricing->update($request->all());

        return response()->json($pricing);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pricing $pricing)
    {
        $pricing->delete();

        return response()->json(['message' => 'Pricing deleted successfully']);
    }
}
