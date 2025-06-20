<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Storage;

class SubCategoryController extends Controller
{
    // List all subcategories with search, pagination, and category relation
public function index(Request $request)
{
    $baseUrl = 'https://api.weddingzadda.com/';
    $query = SubCategory::with('category');

    // Search by name
    if ($search = $request->query('search')) {
        $query->where('name', 'like', '%' . $search . '%');
    }

    // Pagination
    $limit = $request->query('limit', 10);
    $page = $request->query('page', 1);
    $subcategories = $query->paginate($limit, ['*'], 'page', $page);

    // Transform and return only clean array
    $data = $subcategories->getCollection()->transform(function ($subcategory) use ($baseUrl) {
        return [
            'id' => $subcategory->id,
            'name' => $subcategory->name,
            'slug' => $subcategory->slug,
            'description' => $subcategory->description,
            'image' => $subcategory->image ? $baseUrl . $subcategory->image : null,
            'category' => $subcategory->category ? [
                'id' => $subcategory->category->id,
                'name' => $subcategory->category->name,
                'slug' => $subcategory->category->slug,
            ] : null,
        ];
    });

    return response()->json($data);
}
public function fetchAll(Request $request)
{
    $baseUrl = 'https://api.weddingzadda.com/';
    $query = SubCategory::with('vendors');

    // Search by name
    if ($search = $request->query('search')) {
        $query->where('name', 'like', '%' . $search . '%');
    }

    // Pagination
    $limit = $request->query('limit', 10);
    $page = $request->query('page', 1);
    $subcategories = $query->paginate($limit, ['*'], 'page', $page);

    // Transform and return only clean array
    $data = $subcategories->getCollection()->transform(function ($subcategory) use ($baseUrl) {
        return [
            'id' => $subcategory->id,
            'name' => $subcategory->name,
            'slug' => $subcategory->slug,
            'image' => $subcategory->image ? $baseUrl . $subcategory->image : null,
            'vendors' => $subcategory->vendors->map(function ($vendor) use ($baseUrl) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'city' => $vendor->city,
                    'cover_image' => $vendor->cover_image ? $baseUrl . $vendor->cover_image : null,
                ];
            }),
        ];
    });

    return response()->json($data);
}


    // Store a newly created resource in storage
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'category_id' => 'required|integer',
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
                'description' => 'nullable|string',
            ]);
    
            // Check for duplicate subcategory name under the same category
            $existingSubcategory = SubCategory::where('name', $validatedData['name'])
                ->where('category_id', $validatedData['category_id'])
                ->first();
    
            if ($existingSubcategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'The subcategory name already exists for this category.'
                ], 422);
            }
    
            // Generate slug
            $validatedData['slug'] = Str::slug($validatedData['name']);
    
            // Image upload handling
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('subcategories', 'public');
                $validatedData['image'] = 'storage/' . $imagePath;
            }
    
            // Create subcategory
            $subcategory = SubCategory::create($validatedData);
    
            // Append full image URL if exists
            if ($subcategory->image) {
                $subcategory->image = $this->baseUrl . $subcategory->image;
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Subcategory created successfully',
                'subcategory' => $subcategory
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw new HttpResponseException(response()->json([
                'success' => false,
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

    public function update(Request $request, $id): JsonResponse
    {
        $subcategory = SubCategory::find($id);
    
        if (!$subcategory) {
            return response()->json([
                'success' => false,
                'message' => 'Subcategory not found.'
            ], 404);
        }
    
        $validated = $request->validate([
            'category_id' => 'sometimes|required|integer|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'description' => 'sometimes|nullable|string',
        ]);
    
        // Optional: Check for duplicate subcategory name under the same category
        if (isset($validated['name']) && isset($validated['category_id'])) {
            $existing = SubCategory::where('name', $validated['name'])
                ->where('category_id', $validated['category_id'])
                ->where('id', '!=', $id)
                ->first();
    
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subcategory with this name already exists under the selected category.'
                ], 422);
            }
        }
    
        // Generate slug if name is present
        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
    
        // Image upload handling
        if ($request->hasFile('image')) {
            if ($subcategory->image) {
                $oldImagePath = str_replace('storage/', '', $subcategory->image);
                Storage::disk('public')->delete($oldImagePath);
            }
    
            $imagePath = $request->file('image')->store('subcategories', 'public');
            $validated['image'] = 'storage/' . $imagePath;
        }
    
        $subcategory->update($validated);
    
        // Append base URL to image
        if ($subcategory->image) {
            $subcategory->image = url($subcategory->image);
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Subcategory updated successfully.',
            'data' => $subcategory
        ], 200);
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
