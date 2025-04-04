<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // For Debugging


class SubCategoryController extends Controller
{
    // Display a listing of the resource
    // public function index()
    // {
    //     $subcategories = SubCategory::all()->map(function ($subcategory) {
    //         if ($subcategory->image) {
    //             $subcategory->image_url = Storage::disk('public')->url($subcategory->image);
    //         } else {
    //             $subcategory->image_url = null;
    //         }
    //         return $subcategory;
    //     });

    //     return response()->json($subcategories);
    // }
    public function index()
    {
        $baseUrl = config('https://api.weddingzadda.com/');

        $subcategories = SubCategory::all()->map(function ($subcategory) use ($baseUrl) {
            if ($subcategory->image) {
                $subcategory->image_url = $baseUrl . Storage::url($subcategory->image);
            } else {
                $subcategory->image_url = null;
            }
            return $subcategory;
        });

        return response()->json($subcategories);
    }

    public function fetchAll()
{
    $baseUrl = 'https://api.weddingzadda.com/storage/';

    $subcategories = SubCategory::with('vendors')->get()->map(function ($subcategory) use ($baseUrl) {
        return [
            'name' => $subcategory->name,
            'image' => $subcategory->image ? $baseUrl . $subcategory->image : null,
            'vendors' => $subcategory->vendors->map(function ($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'city' => $vendor->city,
                    'cover_image' => $vendor->cover_image,
                    // Add more vendor fields if needed
                ];
            }),
        ];
    });

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

       public function update(Request $request, SubCategory $subcategory)
    {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'category_id' => 'sometimes|required|integer|exists:categories,id',
                'name' => 'sometimes|required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'description' => 'nullable|string',
            ]);

            // Generate slug if name is provided
            if ($request->has('name')) {
                $validatedData['slug'] = Str::slug($request->name);
            }

            // Handle image upload if a new image is present
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($subcategory->image) {
                    Storage::disk('public')->delete($subcategory->image);
                }
                $path = $request->file('image')->store('subcategories', 'public');
                $validatedData['image'] = $path;
            }

            // Update the subcategory
            $subcategory->update($validatedData);

            // Return JSON response
            return response()->json([
                'message' => 'Subcategory updated successfully',
                'subcategory' => $subcategory
            ]);

        } catch (ValidationException $e) {
            // Ensure validation errors return JSON
            throw new HttpResponseException(response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422));
        }
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
