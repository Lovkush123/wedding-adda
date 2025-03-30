<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubCategoryController extends Controller
{
    // Display a listing of the resource
    public function index()
    {
        $subcategories = SubCategory::all();
        return response()->json($subcategories);
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        try {
            // Validate request data (removed 'exists:categories,id')
            $validatedData = $request->validate([
                'category_id' => 'required|integer', // No more "exists" validation
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'description' => 'nullable|string',
            ]);
    
            // Generate slug
            $validatedData['slug'] = Str::slug($request->name);
    
            // Handle image upload if present
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('subcategories', 'public');
                $validatedData['image'] = $path;
            }
    
            // Create the subcategory
            $subcategory = SubCategory::create($validatedData);
    
            // Return JSON response
            return response()->json([
                'message' => 'Subcategory created successfully',
                'subcategory' => $subcategory
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Ensure validation errors return JSON
            throw new HttpResponseException(response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422));
        }
    }
    // Display the specified resource
    public function show(SubCategory $subcategory)
    {
        return response()->json($subcategory);
    }

    // Update the specified resource in storage
    public function update(Request $request, SubCategory $subcategory)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($subcategory->image) {
                Storage::disk('public')->delete($subcategory->image);
            }
            $data['image'] = $request->file('image')->store('subcategories', 'public');
        }

        $subcategory->update($data);
        return response()->json($subcategory);
    }

    // Remove the specified resource from storage
    public function destroy(SubCategory $subcategory)
    {
        if ($subcategory->image) {
            Storage::disk('public')->delete($subcategory->image);
        }
        $subcategory->delete();
        return response()->json(['message' => 'SubCategory deleted successfully']);
    }
}
