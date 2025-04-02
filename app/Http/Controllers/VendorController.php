<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class VendorController extends Controller
{
    // Fetch all categories, subcategories, and vendors
    public function getAllData()
    {
        $categories = Category::with('subCategories')->get();
        $vendors = Vendor::with(['subCategory', 'category'])->get();

        return response()->json([
            'categories' => $categories,
            'vendors' => $vendors,
        ]);
    }

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
                'category_id' => 'required|exists:categories,id',
                'subcategory_id' => 'required|exists:sub_categories,id',
                'address' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'veg_price' => 'nullable|numeric|min:0',
                'non_veg_price' => 'nullable|numeric|min:0',
                'price_type' => 'nullable|in:fixed,variable',
                'starting_price' => 'nullable|numeric|min:0', // Added validation for starting_price
                'ending_price' => 'nullable|numeric|min:0', // Added validation for ending_price
                'about_title' => 'nullable|string|max:255',
                'text_editor' => 'nullable|string',
                'call_number' => 'required|string|unique:vendors',
                'whatsapp_number' => 'nullable|string|unique:vendors',
                'mail_id' => 'required|email|unique:vendors',
                'room_price' => 'nullable|numeric|min:0',
                'cover_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
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
            'category_id' => 'sometimes|exists:categories,id',
            'subcategory_id' => 'sometimes|exists:sub_categories,id',
            'address' => 'sometimes|string|max:255',
            'state' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:255',
            'veg_price' => 'sometimes|nullable|numeric|min:0',
            'non_veg_price' => 'sometimes|nullable|numeric|min:0',
            'price_type' => 'sometimes|nullable|in:fixed,variable',
            'starting_price' => 'sometimes|nullable|numeric|min:0', // Added validation for starting_price
            'ending_price' => 'sometimes|nullable|numeric|min:0', // Added validation for ending_price
            'about_title' => 'sometimes|nullable|string|max:255',
            'text_editor' => 'sometimes|nullable|string',
            'call_number' => 'sometimes|string|unique:vendors,call_number,' . $id,
            'whatsapp_number' => 'sometimes|nullable|string|unique:vendors,whatsapp_number,' . $id,
            'mail_id' => 'sometimes|email|unique:vendors,mail_id,' . $id,
            'room_price' => 'sometimes|nullable|numeric|min:0',
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
