<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log; 
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;




use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
 private $baseUrl = 'https://api.weddingzadda.com/';

// ✅ Cleaned: List categories with optional search and clean response
public function index(Request $request)
{
    $query = Category::with('subcategories');

    // Search by name
    if ($search = $request->query('search')) {
        $query->where('name', 'like', '%' . $search . '%');
    }

    // Pagination
    $limit = $request->query('limit', 10);
    $page = $request->query('page', 1);
    $categories = $query->paginate($limit, ['*'], 'page', $page);

    // Format image URLs
    $data = $categories->getCollection()->transform(function ($category) {
        $category->image = $category->image ? $this->baseUrl . $category->image : null;

        $category->subcategories->transform(function ($subcategory) {
            $subcategory->image = $subcategory->image ? $this->baseUrl . $subcategory->image : null;
            return $subcategory;
        });

        return $category;
    });

    // Return only category list
    return response()->json($data);
}


    
 
    public function store(Request $request): JsonResponse
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        'description' => 'nullable|string',
        'service_id' => 'required|integer|exists:services,id',
    ]);

    // Check for duplicate category name for the same service_id
    $existingCategory = Category::where('name', $validated['name'])
        ->where('service_id', $validated['service_id'])
        ->first();

    if ($existingCategory) {
        return response()->json([
            'success' => false,
            'message' => 'The category name already exists for this service.'
        ], 422);
    }

    // Generate slug
    $validated['slug'] = Str::slug($validated['name']);

    // Image handling
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('categories', 'public');
        $validated['image'] = 'storage/' . $imagePath;
    }

    $category = Category::create($validated);

    // Append full image URL if needed
    if ($category->image) {
        $category->image = $this->baseUrl . $category->image;
    }

    return response()->json([
        'success' => true,
        'message' => 'Category created successfully.',
        'data' => $category
    ], 201);
}

    public function show()
{
    $categories = Category::with(['vendors'])->get()->map(function ($category) {
        return [
            'category' => $category->name,
            'vendors' => $category->vendors->map(function ($vendor) {
                return [
                    'city' => $vendor->city
                ];
            }),
        ];
    });

    return response()->json($categories);
}
    
public function update(Request $request, $id): JsonResponse
{
    $category = Category::find($id);

    if (!$category) {
        return response()->json([
            'success' => false,
            'message' => 'Category not found.'
        ], 404);
    }

    $validated = $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        'description' => 'sometimes|nullable|string',
        'service_id' => 'sometimes|required|integer|exists:services,id',
    ]);

    // Check for duplicate category name for the same service_id (if applicable)
    if (isset($validated['name']) && isset($validated['service_id'])) {
        $existingCategory = Category::where('name', $validated['name'])
            ->where('service_id', $validated['service_id'])
            ->where('id', '!=', $id)
            ->first();

        if ($existingCategory) {
            return response()->json([
                'success' => false,
                'message' => 'The category name already exists for this service.'
            ], 422);
        }
    }

    // Generate slug if name is provided
    if (isset($validated['name'])) {
        $validated['slug'] = Str::slug($validated['name']);
    }

    // Image handling
    if ($request->hasFile('image')) {
        // Delete old image if exists
        if ($category->image) {
            $oldImagePath = str_replace('storage/', '', $category->image);
            Storage::disk('public')->delete($oldImagePath);
        }

        $imagePath = $request->file('image')->store('categories', 'public');
        $validated['image'] = 'storage/' . $imagePath;
    }

    $category->update($validated);

    // Append full URL to image if it exists
    if ($category->image) {
        $category->image = $this->baseUrl . $category->image;
    }

    return response()->json([
        'success' => true,
        'message' => 'Category updated successfully.',
        'data' => $category
    ], 200);
}



    // Remove the specified category
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
