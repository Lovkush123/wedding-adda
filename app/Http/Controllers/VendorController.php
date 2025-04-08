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

class VendorController extends Controller
{
 

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
        $categories = Category::select('id', 'name')
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

   

    // Update vendor with image handling
    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

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
            'cover_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        // Handle new image upload
        if ($request->hasFile('cover_image')) {
            if ($vendor->cover_image) {
                Storage::disk('public')->delete($vendor->cover_image);
            }
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

        if ($vendor->cover_image) {
            Storage::disk('public')->delete($vendor->cover_image);
        }

        $vendor->delete();
        return response()->json(['message' => 'Vendor deleted successfully']);
    }
}
