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
        $vendors = Vendor::with(['subCategory', 'category', 'images', 'features', 'pricing'])->get();

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
    // public function store(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'name' => 'required|string|max:255',
    //             'category_id' => 'required|exists:categories,id',
    //             'subcategory_id' => 'required|exists:sub_categories,id',
    //             'address1' => 'required|string|max:255',
    //             'address2' => 'nullable|string|max:255',
    //             'map_url' => 'nullable|string|max:255',
    //             'state' => 'required|string|max:255',
    //             'city' => 'required|string|max:255',
    //             'country' => 'required|string|max:255',
    //             'price_type' => 'nullable|in:fixed,variable',
    //             'starting_price' => 'nullable|numeric|min:0',
    //             'ending_price' => 'nullable|numeric|min:0',
    //             'about_title' => 'nullable|string|max:255',
    //             'text_editor' => 'nullable|string',
    //             'call_number' => 'required|string|unique:vendors',
    //             'whatsapp_number' => 'nullable|string|unique:vendors',
    //             'mail_id' => 'required|email|unique:vendors',
    //             'cover_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
    //         ]);

    //         // Handle image upload
    //         if ($request->hasFile('cover_image')) {
    //             $imagePath = $request->file('cover_image')->store('vendor_images', 'public');
    //             $validated['cover_image'] = $imagePath;
    //         }

    //         // Create vendor
    //         $vendor = Vendor::create($validated);

    //         return response()->json([
    //             'message' => 'Vendor created successfully',
    //             'vendor' => $vendor
    //         ], 201);

    //     } catch (ValidationException $e) {
    //         throw new HttpResponseException(response()->json([
    //             'message' => 'Validation failed',
    //             'errors' => $e->errors(),
    //         ], 422));
    //     }
    // }
//     public function store(Request $request)
// {
//     try {
//         $data = $request->all();

//         // Handle image upload
//         if ($request->hasFile('cover_image')) {
//             $imagePath = $request->file('cover_image')->store('vendor_images', 'public');
//             $data['cover_image'] = $imagePath;
//         }

//         // Create vendor
//         $vendor = Vendor::create($data);

//         return response()->json([
//             'message' => 'Vendor created successfully',
//             'vendor' => $vendor
//         ], 201);
//     } catch (Exception $e) {
//         return response()->json([
//             'message' => 'An error occurred',
//             'error' => $e->getMessage(),
//         ], 500);
//     }
// }
 // Store a new vendor with related data
 public function store(Request $request)
 {
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
             'price_type' => 'nullable|in:fixed,variable',
             'starting_price' => 'nullable|numeric|min:0',
             'ending_price' => 'nullable|numeric|min:0',
             'about_title' => 'nullable|string|max:255',
             'text_editor' => 'nullable|string',
             'call_number' => 'required|string|unique:vendors',
             'whatsapp_number' => 'nullable|string|unique:vendors',
             'mail_id' => 'required|email|unique:vendors',
             'cover_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
         ]);

         if ($request->hasFile('cover_image')) {
             $imagePath = $request->file('cover_image')->store('vendor_images', 'public');
             $validated['cover_image'] = $imagePath;
         }

         $vendor = Vendor::create($validated);

         if ($request->hasFile('images')) {
             foreach ($request->file('images') as $image) {
                 $imagePath = $image->store('vendor_images', 'public');
                 Image::create(['vendor_id' => $vendor->id, 'image' => $imagePath]);
             }
         }

         if ($request->has('features')) {
             foreach ($request->input('features') as $feature) {
                 Feature::create([
                     'vendor_id' => $vendor->id,
                     'title' => $feature['title'],
                     'description' => $feature['description'],
                 ]);
             }
         }

         if ($request->has('pricing')) {
             foreach ($request->input('pricing') as $price) {
                 Pricing::create([
                     'vendor_id' => $vendor->id,
                     'price_name' => $price['price_name'],
                     'price_type' => $price['price_type'],
                     'price_category' => $price['price_category'],
                     'price' => $price['price'],
                 ]);
             }
         }

         return response()->json([
             'message' => 'Vendor and related data created successfully',
             'vendor' => $vendor
         ], 201);
     } catch (ValidationException $e) {
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
            'address1' => 'sometimes|string|max:255',
            'address2' => 'sometimes|nullable|string|max:255',
            'map_url' => 'sometimes|nullable|string|max:255',
            'state' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:255',
            'price_type' => 'sometimes|nullable|in:fixed,variable',
            'starting_price' => 'sometimes|nullable|numeric|min:0',
            'ending_price' => 'sometimes|nullable|numeric|min:0',
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
