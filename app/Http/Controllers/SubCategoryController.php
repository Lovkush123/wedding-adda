<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // For Debugging
use Illuminate\Http\JsonResponse;

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
//     public function index()
//     {
//         $baseUrl = config('https://api.weddingzadda.com/');

//         $subcategories = SubCategory::all()->map(function ($subcategory) use ($baseUrl) {
//             if ($subcategory->image) {
//                 $subcategory->image_url = $baseUrl . Storage::url($subcategory->image);
//             } else {
//                 $subcategory->image_url = null;
//             }
//             return $subcategory;
//         });

//         return response()->json($subcategories);
//     }

//     public function fetchAll()
// {
//     $baseUrl = 'https://api.weddingzadda.com/storage/';

//     $subcategories = SubCategory::with('vendors')->get()->map(function ($subcategory) use ($baseUrl) {
//         return [
//             'name' => $subcategory->name,
//             'image' => $subcategory->image ? $baseUrl . $subcategory->image : null,
//             'vendors' => $subcategory->vendors->map(function ($vendor) {
//                 return [
//                     'id' => $vendor->id,
//                     'name' => $vendor->name,
//                     'city' => $vendor->city,
//                     'cover_image' => $vendor->cover_image,
//                     // Add more vendor fields if needed
//                 ];
//             }),
//         ];
//     });

//     return response()->json($subcategories);
// }
  // Fetch all subcategories with full image URL
  public function index()
  {
      $baseUrl = 'https://api.weddingzadda.com/';

      $subcategories = SubCategory::with('category')->get()->map(function ($subcategory) use ($baseUrl) {
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

      return response()->json($subcategories);
  }

  // Fetch subcategories with vendors
  public function fetchAll()
  {
      $baseUrl = 'https://api.weddingzadda.com/';

      $subcategories = SubCategory::with('vendors')->get()->map(function ($subcategory) use ($baseUrl) {
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
                      // Add more fields if needed
                  ];
              }),
          ];
      });

      return response()->json($subcategories);
  }
    // Store a newly created resource in storage
    public function store(Request $request): JsonResponse
    {
        $baseUrl = 'https://api.weddingzadda.com/';
        
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
                $subcategory->image = $baseUrl . $subcategory->image;
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
