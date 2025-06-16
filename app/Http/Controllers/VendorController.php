<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Image;
use App\Models\Feature;
use App\Models\Pricing;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
class VendorController extends Controller
{
 
 public function getUniqueCities()
    {
        try {
            $cities = Vendor::select('city')
                            ->distinct()
                            ->orderBy('city')
                            ->pluck('city'); // pluck 'city' to get a simple array of city names

            return response()->json(['cities' => $cities]);
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            \Log::error('Error fetching unique cities: ' . $e->getMessage());
            return response()->json(['message' => 'Could not fetch cities', 'error' => $e->getMessage()], 500);
        }
    }

    public function fetchVendorDetails($id = null)
    {
        $query = Vendor::with(['images:id,vendor_id,image', 'features:id,vendor_id,title,description', 'pricing:id,vendor_id,price,price_name,price_type,price_category']);

        if ($id) {
            $vendor = $query->find($id);

            if (!$vendor) {
                return response()->json(['message' => 'Vendor not found'], 404);
            }

            return response()->json(['vendor' => $vendor]);
        }

        $vendors = $query->get();
        return response()->json(['vendors' => $vendors]);
    }
    
    // Show a single vendor or all vendors
    public function show($id = null)
    {
        return $this->fetchVendorDetails($id);
    }
    // Fetch all categories, subcategories, and vendors
    
    public function getAllData()
    {
        $categories = Category::select('id', 'name', 'slug', 'image', 'description')
            ->with([
                'subcategories:id,category_id,name,slug,image,description',
                'subcategories.vendors' => function ($vendorQuery) {
                    $vendorQuery->select(
                        'id',
                        'name',
                        'slug',
                        'category_id',
                        'subcategory_id',
                        'address1',
                        'address2',
                        'map_url',
                        'state',
                        'city',
                        'country',
                        'based_area',
                        'short_description',
                        'about_title',
                        'text_editor',
                        'call_number',
                        'whatsapp_number',
                        'mail_id',
                        'cover_image'
                    )
                    ->with([
                        'images:id,vendor_id,image',
                        'features:id,vendor_id,title,description',
                        'pricing:id,vendor_id,price,price_name,price_type,price_category'
                    ]);
                }
            ])
            ->get();

        return response()->json(['categories' => $categories]);
    }

    public function getCategoryDataBySlug($slug)
    {
        // Fetch category by slug with nested subcategories and vendor details
        $category = Category::where('slug', $slug)
            ->with([
                'subCategories' => function ($subQuery) {
                    $subQuery->select('id', 'category_id', 'name', 'slug', 'image', 'description')
                        ->with([
                            'vendors' => function ($vendorQuery) {
                                $vendorQuery->select(
                                    'id', 'name', 'slug', 'category_id', 'subcategory_id',
                                    'address1', 'address2', 'map_url', 'state', 'city', 'country',
                                    'based_area', 'short_description', 'about_title', 'text_editor',
                                    'call_number', 'whatsapp_number', 'mail_id', 'cover_image'
                                )
                                ->with([
                                    'images:id,vendor_id,image',
                                    'features:id,vendor_id,title,description',
                                    'pricing:id,vendor_id,price,price_name,price_type,price_category'
                                ]);
                            }
                        ]);
                }
            ])
            ->select('id', 'name', 'slug', 'image', 'description') // Include any fields you need
            ->first();
    
        if (!$category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }
    
        return response()->json(['category' => $category]);
    }
    
    public function getVendorsBySubCategorySlug($slug)
    {
        $subCategory = SubCategory::where('slug', $slug)->first();

        if (!$subCategory) {
            return response()->json(['message' => 'Subcategory not found'], 404);
        }

        $vendors = Vendor::where('subcategory_id', $subCategory->id)
            ->with([
                'images:id,vendor_id,image',
                'features:id,vendor_id,title,description',
                'pricing:id,vendor_id,price,price_name,price_type,price_category'
            ])
            ->get();

        return response()->json([
            'subcategory' => $subCategory->only(['id', 'name', 'slug', 'description', 'image']),
            'vendors' => $vendors
        ]);
    }
    
    public function getVendorBySlug($slug)
    {
        $vendor = Vendor::where('slug', $slug)
            ->with([
                'images:id,vendor_id,image',
                'features:id,vendor_id,title,description',
                'pricing:id,vendor_id,price,price_name,price_type,price_category'
            ])
            ->first();

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        return response()->json([
            'vendor' => $vendor
        ]);
    }

// public function filterVendors(Request $request): JsonResponse
// {
//     $categoryName = $request->query('category');
//     $subcategoryName = $request->query('subcategory');
//     $city = $request->query('city');
//     $state = $request->query('state');

//     $vendorsQuery = Vendor::query()
//         ->select(
//             'id', 'name', 'slug', 'category_id', 'subcategory_id',
//             'address1', 'address2', 'map_url', 'state', 'city', 'country',
//             'based_area', 'short_description', 'about_title', 'text_editor',
//             'call_number', 'whatsapp_number', 'mail_id', 'cover_image'
//         )
//         ->with([
//             'images:id,vendor_id,image',
//             'features:id,vendor_id,title,description',
//             'pricing:id,vendor_id,price,price_name,price_type,price_category'
//         ]);

//     // Filter by category name
//     if ($categoryName) {
//         $category = Category::where('name', $categoryName)->first();
//         if (!$category) {
//             return response()->json(['message' => 'Category not found.'], 404);
//         }
//         $vendorsQuery->where('category_id', $category->id);
//     }

//     // Filter by subcategory name
//     if ($subcategoryName) {
//         $subCategory = SubCategory::where('name', $subcategoryName)->first();
//         if (!$subCategory) {
//             return response()->json(['message' => 'Subcategory not found.'], 404);
//         }
//         $vendorsQuery->where('subcategory_id', $subCategory->id);
//     }

//     // Filter by city
//     if ($city) {
//         $vendorsQuery->where('city', $city);
//     }

//     // Filter by state
//     if ($state) {
//         $vendorsQuery->where('state', $state);
//     }

//     $vendors = $vendorsQuery->get();

//     return response()->json([
//         'filters' => [
//             'category' => $categoryName,
//             'subcategory' => $subcategoryName,
//             'city' => $city,
//             'state' => $state,
//         ],
//         'vendors' => $vendors
//     ]);
// }
// public function filterVendors(Request $request): JsonResponse
// {
//     $categoryName = $request->query('category');
//     $subcategoryName = $request->query('subcategory');
//     $city = $request->query('city');
//     $state = $request->query('state');

//     $vendorsQuery = Vendor::query()
//         ->select(
//             'id', 'name', 'slug', 'category_id', 'subcategory_id',
//             'address1', 'address2', 'map_url', 'state', 'city', 'country',
//             'based_area', 'short_description', 'about_title', 'text_editor',
//             'call_number', 'whatsapp_number', 'mail_id', 'cover_image'
//         )
//         ->with([
//             'images:id,vendor_id,image',
//             'features:id,vendor_id,title,description',
//             'pricing:id,vendor_id,price,price_name,price_type,price_category'
//         ]);

//     // Apply filters only if present
//     if ($categoryName) {
//         $category = Category::where('name', $categoryName)->first();
//         if (!$category) {
//             return response()->json(['message' => 'Category not found.'], 404);
//         }
//         $vendorsQuery->where('category_id', $category->id);
//     }

//     if ($subcategoryName) {
//         $subCategory = SubCategory::where('name', $subcategoryName)->first();
//         if (!$subCategory) {
//             return response()->json(['message' => 'Subcategory not found.'], 404);
//         }
//         $vendorsQuery->where('subcategory_id', $subCategory->id);
//     }

//     if ($city) {
//         $vendorsQuery->where('city', $city);
//     }

//     if ($state) {
//         $vendorsQuery->where('state', $state);
//     }

//     // Get all or filtered vendors
//     $vendors = $vendorsQuery->get();

//     return response()->json([
//         'filters' => [
//             'category' => $categoryName,
//             'subcategory' => $subcategoryName,
//             'city' => $city,
//             'state' => $state,
//         ],
//         'vendors' => $vendors
//     ]);
// }

// public function filterVendors(Request $request): JsonResponse
// {
//     $categoryName = $request->query('category');
//     $subcategoryName = $request->query('subcategory');
//     $city = $request->query('city');
//     $state = $request->query('state');
//     $limit = $request->query('limit', 10); // default 10
//     $page = $request->query('page', 1); // default 1

//     $vendorsQuery = Vendor::query()
//         ->select(
//             'id', 'name', 'slug', 'category_id', 'subcategory_id',
//             'address1', 'address2', 'map_url', 'state', 'city', 'country',
//             'based_area', 'short_description', 'about_title', 'text_editor',
//             'call_number', 'whatsapp_number', 'mail_id', 'cover_image'
//         )
//         ->with([
//             'images:id,vendor_id,image',
//             'features:id,vendor_id,title,description',
//             'pricing:id,vendor_id,price,price_name,price_type,price_category'
//         ]);

//     // Apply filters
//     if ($categoryName) {
//         $category = Category::where('name', $categoryName)->first();
//         if (!$category) {
//             return response()->json(['message' => 'Category not found.'], 404);
//         }
//         $vendorsQuery->where('category_id', $category->id);
//     }

//     if ($subcategoryName) {
//         $subCategory = SubCategory::where('name', $subcategoryName)->first();
//         if (!$subCategory) {
//             return response()->json(['message' => 'Subcategory not found.'], 404);
//         }
//         $vendorsQuery->where('subcategory_id', $subCategory->id);
//     }

//     if ($city) {
//         $vendorsQuery->where('city', $city);
//     }

//     if ($state) {
//         $vendorsQuery->where('state', $state);
//     }

//     // Apply pagination
//     $vendors = $vendorsQuery->paginate($limit, ['*'], 'page', $page);

//     return response()->json([
//         'filters' => [
//             'category' => $categoryName,
//             'subcategory' => $subcategoryName,
//             'city' => $city,
//             'state' => $state,
//             'limit' => $limit,
//             'page' => $page,
//         ],
//         'vendors' => $vendors
//     ]);
// }

public function filterVendors(Request $request): JsonResponse
{
    $categoryName = $request->query('category');
    $subcategoryName = $request->query('subcategory');
    $city = $request->query('city');
    $state = $request->query('state');
    $search = $request->query('search'); // search by vendor name
    $limit = $request->query('limit', 10);
    $page = $request->query('page', 1);

    $vendorsQuery = Vendor::query()
        ->select(
            'id', 'name', 'slug', 'category_id', 'subcategory_id',
            'address1', 'address2', 'map_url', 'state', 'city', 'country',
            'based_area', 'short_description', 'about_title', 'text_editor',
            'call_number', 'whatsapp_number', 'mail_id', 'cover_image','user_id'
        )
        ->with([
            'images:id,vendor_id,image',
            'features:id,vendor_id,title,description',
            'pricing:id,vendor_id,price,price_name,price_type,price_category'
        ]);

    // Apply filters
    if ($categoryName) {
        $category = Category::where('name', $categoryName)->first();
        if (!$category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }
        $vendorsQuery->where('category_id', $category->id);
    }

    if ($subcategoryName) {
        $subCategory = SubCategory::where('name', $subcategoryName)->first();
        if (!$subCategory) {
            return response()->json(['message' => 'Subcategory not found.'], 404);
        }
        $vendorsQuery->where('subcategory_id', $subCategory->id);
    }

    if ($city) {
        $vendorsQuery->where('city', $city);
    }

    if ($state) {
        $vendorsQuery->where('state', $state);
    }

    // Apply search by vendor name
    if ($search) {
        $vendorsQuery->where('name', 'like', '%' . $search . '%');
    }

    // Apply pagination
    $vendors = $vendorsQuery->paginate($limit, ['*'], 'page', $page);

    return response()->json([
        'filters' => [
            'category' => $categoryName,
            'subcategory' => $subcategoryName,
            'city' => $city,
            'state' => $state,
            'search' => $search,
            'limit' => $limit,
            'page' => $page,
        ],
        'vendors' => $vendors
    ]);
}


    // List all vendors
    public function index()
    {
        return response()->json(Vendor::all());
    }


public function store(Request $request)
{
    DB::beginTransaction();
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:sub_categories,id',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'map_url' => 'nullable|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'based_area' => 'nullable|string|max:512',
            'short_description' => 'nullable|string|max:512',
            'about_title' => 'nullable|string|max:255',
            'text_editor' => 'nullable|string',
            'call_number' => 'required|string|unique:vendors',
            'whatsapp_number' => 'nullable|string|unique:vendors',
            'mail_id' => 'required|email|unique:vendors',
            'cover_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'features' => 'nullable|array',
            'features.*.title' => 'required_with:features|string|max:255',
            'features.*.description' => 'required_with:features|string',
            'pricing' => 'nullable|array',
            'pricing.*.price_name' => 'required_with:pricing|string|max:255',
            'pricing.*.price_type' => 'required_with:pricing|in:package,variable',
            'pricing.*.price_category' => 'required_with:pricing|string|max:255',
            'pricing.*.price' => 'required_with:pricing|numeric|min:0',
        ]);

        // Generate slug based on name, based_area, and city
        $name = $validated['name'];
        $based_area = $validated['based_area'] ?? '';
        $city = $validated['city'];
        
        $slug = Str::slug("{$name}-{$based_area}-{$city}");
        
        // Ensure slug uniqueness
        $count = Vendor::where('slug', 'LIKE', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $validated['slug'] = $slug;

        if ($request->hasFile('cover_image')) {
            $imagePath = $request->file('cover_image')->store('vendor_images', 'public');
            $validated['cover_image'] = $imagePath;
        }

        $vendor = Vendor::create($validated);

        if ($request->hasFile('images')) {
            $imageData = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('vendor_images', 'public');
                $imageData[] = ['vendor_id' => $vendor->id, 'image' => $imagePath];
            }
            Image::insert($imageData);
        }

        if ($request->has('features')) {
            $featureData = [];
            foreach ($request->input('features') as $feature) {
                $featureData[] = [
                    'vendor_id' => $vendor->id,
                    'title' => $feature['title'],
                    'description' => $feature['description'],
                ];
            }
            Feature::insert($featureData);
        }

        if ($request->has('pricing')) {
            $pricingData = [];
            foreach ($request->input('pricing') as $price) {
                $pricingData[] = [
                    'vendor_id' => $vendor->id,
                    'price_name' => $price['price_name'],
                    'price_type' => $price['price_type'],
                    'price_category' => $price['price_category'],
                    'price' => $price['price'],
                ];
            }
            Pricing::insert($pricingData);
        }

        DB::commit();

        return response()->json([
            'message' => 'Vendor and related data created successfully',
            'vendor' => $vendor
        ], 201);
    } catch (ValidationException $e) {
        DB::rollBack();
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422));
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'An error occurred',
            'error' => $e->getMessage(),
        ], 500);
    }
}

   

public function update(Request $request, $id): JsonResponse
{
    $vendor = Vendor::find($id);

    if (!$vendor) {
        return response()->json([
            'success' => false,
            'message' => 'Vendor not found.'
        ], 404);
    }

    $validated = $request->validate([
        'name' => 'sometimes|string|max:255',
        'category_id' => 'sometimes|exists:categories,id',
        'subcategory_id' => 'sometimes|exists:sub_categories,id',
        'address1' => 'sometimes|string|max:255',
        'address2' => 'sometimes|nullable|string|max:255',
        'map_url' => 'sometimes|nullable|string|max:255',
        'state' => 'sometimes|string|max:255',
        'city' => 'sometimes|string|max:255',
        'country' => 'sometimes|string|max:255',
        'based_area' => 'nullable|string|max:512',
        'short_description' => 'nullable|string|max:512',
        'about_title' => 'sometimes|nullable|string|max:255',
        'text_editor' => 'sometimes|nullable|string',
        'call_number' => 'sometimes|string|unique:vendors,call_number,' . $id,
        'whatsapp_number' => 'sometimes|nullable|string|unique:vendors,whatsapp_number,' . $id,
        'mail_id' => 'sometimes|email|unique:vendors,mail_id,' . $id,
        'cover_image' => 'sometimes|nullable|image|mimes:jpg,png,jpeg|max:2048',
    ]);

    // Image handling
    if ($request->hasFile('cover_image')) {
        if ($vendor->cover_image) {
            $oldImagePath = str_replace('storage/', '', $vendor->cover_image);
            Storage::disk('public')->delete($oldImagePath);
        }

        $imagePath = $request->file('cover_image')->store('vendor_images', 'public');
        $validated['cover_image'] = 'storage/' . $imagePath;
    }

    // Optional slug generation based on vendor name
    if (isset($validated['name'])) {
        $validated['slug'] = Str::slug($validated['name']);
    }

    $vendor->update($validated);

    // Return full image URL if exists
    if ($vendor->cover_image) {
        $vendor->cover_image = url($vendor->cover_image);
    }

    return response()->json([
        'success' => true,
        'message' => 'Vendor updated successfully.',
        'data' => $vendor
    ], 200);
}

    // Delete vendor and remove image
    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);

        if ($vendor->cover_image) {
            Storage::disk('public')->delete($vendor->cover_image);
        }

        $vendor->delete();
        return response()->json(['message' => 'Vendor deleted successfully']);
    }



    // public function fetchBySlugs($category_slug, $subcategory_slug = null, $vendor_slug = null)
    // {
    //     $category = Category::where('slug', $category_slug)
    //         ->select('id', 'name', 'slug', 'image', 'description')
    //         ->first();
    
    //     if (!$category) {
    //         return response()->json(['error' => 'Category not found'], 404);
    //     }
    
    //     // Case 1: Only category slug
    //     if (!$subcategory_slug && !$vendor_slug) {
    //         $category->load([
    //             'subcategories:id,category_id,name,slug,image,description',
    //             'subcategories.vendors' => function ($query) {
    //                 $query->select(
    //                     'id', 'name', 'slug', 'category_id', 'subcategory_id', 'address1',
    //                     'address2', 'map_url', 'state', 'city', 'country', 'based_area',
    //                     'short_description', 'about_title', 'text_editor', 'call_number',
    //                     'whatsapp_number', 'mail_id', 'cover_image'
    //                 );
    //             }
    //         ]);
    //         return response()->json(['category' => $category]);
    //     }
    
    //     $subcategory = $category->subcategories()
    //         ->where('slug', $subcategory_slug)
    //         ->select('id', 'name', 'slug', 'category_id', 'image', 'description')
    //         ->first();
    
    //     if (!$subcategory) {
    //         return response()->json(['error' => 'Subcategory not found'], 404);
    //     }
    
    //     // Case 2: Category + Subcategory slugs
    //     if (!$vendor_slug) {
    //         $subcategory->load([
    //             'vendors' => function ($query) {
    //                 $query->select(
    //                     'id', 'name', 'slug', 'category_id', 'subcategory_id', 'address1',
    //                     'address2', 'map_url', 'state', 'city', 'country', 'based_area',
    //                     'short_description', 'about_title', 'text_editor', 'call_number',
    //                     'whatsapp_number', 'mail_id', 'cover_image'
    //                 );
    //             }
    //         ]);
    //         return response()->json(['subcategory' => $subcategory]);
    //     }
    
    //     // Case 3: Category + Subcategory + Vendor slugs
    //     $vendor = $subcategory->vendors()
    //         ->where('slug', $vendor_slug)
    //         ->select(
    //             'id', 'name', 'slug', 'category_id', 'subcategory_id', 'address1', 'address2',
    //             'map_url', 'state', 'city', 'country', 'based_area', 'short_description',
    //             'about_title', 'text_editor', 'call_number', 'whatsapp_number', 'mail_id',
    //             'cover_image'
    //         )
    //         ->with([
    //             'images:id,vendor_id,image',
    //             'features:id,vendor_id,title,description',
    //             'pricing:id,vendor_id,price,price_name,price_type,price_category'
    //         ])
    //         ->first();
    
    //     if (!$vendor) {
    //         return response()->json(['error' => 'Vendor not found'], 404);
    //     }
    
    //     return response()->json(['vendor' => $vendor]);
    // }
    
    public function fetchBySlugs($category_slug = null, $subcategory_slug = null, $vendor_slug = null)
    {
        // Case 1: /all => All categories with subcategories and vendors
        if ($category_slug === 'all') {
            $categories = Category::select('id', 'name', 'slug', 'image', 'description')
                ->with([
                    'subcategories:id,category_id,name,slug,image,description',
                    'subcategories.vendors' => function ($query) {
                        $query->select(
                            'id', 'name', 'slug', 'category_id', 'subcategory_id', 'address1',
                            'address2', 'map_url', 'state', 'city', 'country', 'based_area',
                            'short_description', 'about_title', 'text_editor', 'call_number',
                            'whatsapp_number', 'mail_id', 'cover_image'
                        );
                    }
                ])
                ->get();
    
            return response()->json(['categories' => $categories]);
        }
    
        // Fetch category if not 'all'
        $category = Category::where('slug', $category_slug)
            ->select('id', 'name', 'slug', 'image', 'description')
            ->first();
    
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }
    
        // Case 2: /category_slug or /category_slug/all
        if (!$subcategory_slug || $subcategory_slug === 'all') {
            $category->load([
                'subcategories:id,category_id,name,slug,image,description',
                'subcategories.vendors' => function ($query) {
                    $query->select(
                        'id', 'name', 'slug', 'category_id', 'subcategory_id', 'address1',
                        'address2', 'map_url', 'state', 'city', 'country', 'based_area',
                        'short_description', 'about_title', 'text_editor', 'call_number',
                        'whatsapp_number', 'mail_id', 'cover_image'
                    );
                }
            ]);
    
            return response()->json(['category' => $category]);
        }
    
        // Fetch subcategory if not 'all'
        $subcategory = $category->subcategories()
            ->where('slug', $subcategory_slug)
            ->select('id', 'name', 'slug', 'category_id', 'image', 'description')
            ->first();
    
        if (!$subcategory) {
            return response()->json(['error' => 'Subcategory not found'], 404);
        }
    
        // Case 3: /category_slug/subcategory_slug or /category_slug/subcategory_slug/all
        if (!$vendor_slug || $vendor_slug === 'all') {
            $subcategory->load([
                'vendors' => function ($query) {
                    $query->select(
                        'id', 'name', 'slug', 'category_id', 'subcategory_id', 'address1',
                        'address2', 'map_url', 'state', 'city', 'country', 'based_area',
                        'short_description', 'about_title', 'text_editor', 'call_number',
                        'whatsapp_number', 'mail_id', 'cover_image'
                    );
                }
            ]);
    
            return response()->json(['subcategory' => $subcategory]);
        }
    
        // Case 4: Specific vendor
        $vendor = $subcategory->vendors()
            ->where('slug', $vendor_slug)
            ->select(
                'id', 'name', 'slug', 'category_id', 'subcategory_id', 'address1', 'address2',
                'map_url', 'state', 'city', 'country', 'based_area', 'short_description',
                'about_title', 'text_editor', 'call_number', 'whatsapp_number', 'mail_id',
                'cover_image'
            )
            ->with([
                'images:id,vendor_id,image',
                'features:id,vendor_id,title,description',
                'pricing:id,vendor_id,price,price_name,price_type,price_category'
            ])
            ->first();
    
        if (!$vendor) {
            return response()->json(['error' => 'Vendor not found'], 404);
        }
    
        return response()->json(['vendor' => $vendor]);
    }
    


}
