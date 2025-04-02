<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feature;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class FeatureController extends Controller
{
    // Get all features
    public function index()
    {
        return response()->json(Feature::all());
    }

    // Store a new feature
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'vendor_id' => 'required|exists:vendors,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $feature = Feature::create($validated);
            return response()->json(['message' => 'Feature created successfully', 'feature' => $feature], 201);
        } catch (ValidationException $e) {
            throw new HttpResponseException(response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422));
        }
    }

    // Show a single feature
    public function show($id)
    {
        $feature = Feature::findOrFail($id);
        return response()->json($feature);
    }

    // Update a feature
    public function update(Request $request, $id)
    {
        $feature = Feature::findOrFail($id);

        $validated = $request->validate([
            'vendor_id' => 'sometimes|exists:vendors,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
        ]);

        $feature->update($validated);
        return response()->json(['message' => 'Feature updated successfully', 'feature' => $feature]);
    }

    // Delete a feature
    public function destroy($id)
    {
        $feature = Feature::findOrFail($id);
        $feature->delete();
        return response()->json(['message' => 'Feature deleted successfully']);
    }
}
