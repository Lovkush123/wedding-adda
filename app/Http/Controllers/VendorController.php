<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
class VendorController extends Controller
{
    // List all vendors
    public function index()
    {
        return response()->json(Vendor::all());
    }

    // Store a new vendor with image upload
    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'non_veg' => 'boolean',
            'veg' => 'boolean',
            'starting_price' => 'required|numeric|min:0',
            'contact' => 'required|string|unique:vendors',
            'mail' => 'required|email|unique:vendors',
            'cover_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Image validation
        ]);

        // Handle image upload
        if ($request->hasFile('cover_image')) {
            $imagePath = $request->file('cover_image')->store('vendor_images', 'public');
            $validated['cover_image'] = $imagePath;
        }

        // Create vendor
        $vendor = Vendor::create($validated);

        return response()->json([
            'message' => 'Vendor created successfully',
            'vendor' => $vendor
        ], 201);

    } catch (ValidationException $e) {
        // Handle validation errors and return JSON response
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422));
    }
}

    // Show single vendor
    public function show($id)
    {
        $vendor = Vendor::findOrFail($id);
        return response()->json($vendor);
    }

    // Update vendor with image handling
    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'sub_category_id' => 'sometimes|exists:sub_categories,id',
            'non_veg' => 'sometimes|boolean',
            'veg' => 'sometimes|boolean',
            'starting_price' => 'sometimes|numeric|min:0',
            'contact' => 'sometimes|string|unique:vendors,contact,' . $id,
            'mail' => 'sometimes|email|unique:vendors,mail,' . $id,
            'cover_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        // Handle new image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($vendor->cover_image) {
                Storage::disk('public')->delete($vendor->cover_image);
            }
            // Store new image
            $imagePath = $request->file('cover_image')->store('vendor_images', 'public');
            $validated['cover_image'] = $imagePath;
        }

        $vendor->update($validated);
        return response()->json($vendor);
    }

    // Delete vendor and remove image
    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);

        // Delete associated image
        if ($vendor->cover_image) {
            Storage::disk('public')->delete($vendor->cover_image);
        }

        $vendor->delete();
        return response()->json(['message' => 'Vendor deleted successfully']);
    }
}
