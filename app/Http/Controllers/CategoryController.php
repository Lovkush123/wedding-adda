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
  public function update(Request $request, $id)
  {
      $category = Category::findOrFail($id);

      $request->validate([
          'name' => 'required|string|max:255',
          'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
          'description' => 'nullable|string',
          'service_id' => 'required|integer',
      ]);

      $data = $request->all();
      $data['slug'] = Str::slug($request->name);

      if ($request->hasFile('image')) {
          // Delete old image if it exists
          if ($category->image && Storage::disk('public')->exists($category->image)) {
              Storage::disk('public')->delete($category->image);
          }

          $path = $request->file('image')->store('categories', 'public');
          $data['image'] = $path;
      }

      $category->update($data);

      return response()->json([
          'status' => 200,
          'message' => 'Category updated successfully.',
          'data' => $category,
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
