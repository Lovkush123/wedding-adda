<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log; 
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // Display a listing of categories
    private $baseUrl = 'https://api.weddingzadda.com/storage/';

    // Display a listing of categories
    // public function index()
    // {
    //     $categories = Category::all()->map(function ($category) {
    //         $category->image = $category->image ? $this->baseUrl . $category->image : null;
    //         return $category;
    //     });
    //     return response()->json($categories);
    // }
    public function index()
    {
        $categories = Category::with('subcategories')->get()->map(function ($category) {
            $category->image = $category->image ? $this->baseUrl . $category->image : null;

            // Map subcategories if needed
            $category->subcategories->map(function ($subcategory) {
                $subcategory->image = $subcategory->image ? $this->baseUrl . $subcategory->image : null;
                return $subcategory;
            });

            return $category;
        });

        return response()->json($categories);
    }

    
    // Store a newly created category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'service_id' => 'required|integer',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $data['image'] = $path;
        }

        $category = Category::create($data);
        return response()->json($category, 201);
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
    // Display the specified category
    // public function show($id)
    // {
    //     $category = Category::findOrFail($id);
    //     return response()->json($category);
    // }

    // Update the specified category
  // Update an existing category
//   public function update(Request $request, $id)
//   {
//       try {
//           $category = Category::findOrFail($id);
  
//           $validatedData = $request->validate([
//               'name' => 'required|string|max:255',
//               'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
//               'description' => 'nullable|string',
//               'service_id' => 'required|integer',
//           ]);
  
//           $category->name = $validatedData['name'];
//           $category->slug = \Str::slug($validatedData['name']);
//           $category->description = $validatedData['description'] ?? $category->description;
//           $category->service_id = $validatedData['service_id'];
  
//           if ($request->hasFile('image')) {
//               if ($category->image && \Storage::disk('public')->exists($category->image)) {
//                   \Storage::disk('public')->delete($category->image);
//               }
  
//               $path = $request->file('image')->store('categories', 'public');
//               $category->image = $path;
//           }
  
//           $category->save();
  
//           return response()->json([
//               'status' => 200,
//               'message' => 'Category updated successfully.',
//               'data' => $category,
//           ]);
//       } catch (\Illuminate\Validation\ValidationException $e) {
//           return response()->json([
//               'status' => 422,
//               'message' => 'Validation failed.',
//               'errors' => $e->errors()
//           ]);
//       } catch (\Exception $e) {
//           return response()->json([
//               'status' => 500,
//               'message' => 'Something went wrong.',
//               'error' => $e->getMessage()
//           ]);
//       }
//   }
public function update(Request $request, $id)
{
    try {
        $category = Category::findOrFail($id);

        // Log or debug request data (optional)
        // Log::info('Update category request:', $request->all());

        // Validate request (recommended)
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'service_id' => 'nullable|integer|exists:services,id',
            'image' => 'nullable|image|max:2048',
        ]);

        // Only update fields that are present in request
        if ($request->filled('name')) {
            $category->name = $request->input('name');
            $category->slug = Str::slug($category->name);
        }

        if ($request->filled('description')) {
            $category->description = $request->input('description');
        }

        if ($request->filled('service_id')) {
            $category->service_id = $request->input('service_id');
        }

        // Handle image update
        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $path = $request->file('image')->store('categories', 'public');
            $category->image = $path;
        }

        // Save the category
        $category->save();

        return response()->json([
            'status' => 200,
            'message' => 'Category updated successfully.',
            'data' => $category,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage(),
        ]);
    }
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
