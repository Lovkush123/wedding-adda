<?php

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;

Route::get('/users', [UserController::class, 'index']); // Get all users
Route::post('/users', [UserController::class, 'store']); // Create a user
Route::get('/users/{id}', [UserController::class, 'show']); // Get a single user
Route::put('/users/{id}', [UserController::class, 'update']); // Update a user
Route::delete('/users/{id}', [UserController::class, 'destroy']); // Delete a user
 Route::post('/login', [UserController::class, 'login']);   // Login user

Route::get('/addresses', [AddressController::class, 'index']); // Fetch all addresses
Route::post('/addresses', [AddressController::class, 'store']); // Create a new address
Route::get('/addresses/{address}', [AddressController::class, 'show']); // Fetch a single address
Route::put('/addresses/{address}', [AddressController::class, 'update']); // Update an address
Route::delete('/addresses/{address}', [AddressController::class, 'destroy']); // Delete an address


Route::get('/services', [ServiceController::class, 'index']); // List all services
Route::post('/services', [ServiceController::class, 'store']); // Create a new service
Route::get('/services/{service}', [ServiceController::class, 'show']); // Get a single service
Route::put('/services/{service}', [ServiceController::class, 'update']); // Update a service
Route::delete('/services/{service}', [ServiceController::class, 'destroy']); // Delete a service



// Get all gallery images
Route::get('/gallery', [GalleryController::class, 'index']);

// Upload a new image
Route::post('/gallery', [GalleryController::class, 'store']);

// Get a single gallery image by ID
Route::get('/gallery/{gallery}', [GalleryController::class, 'show']);

// Update an existing gallery image
Route::put('/gallery/{gallery}', [GalleryController::class, 'update']);

// Delete a gallery image
Route::delete('/gallery/{gallery}', [GalleryController::class, 'destroy']);



// Get all communities
Route::get('/communities', [CommunityController::class, 'index']);

// Get a single community by ID
Route::get('/communities/{id}', [CommunityController::class, 'show']);

// Create a new community
Route::post('/communities', [CommunityController::class, 'store']);

// Update a community by ID
Route::post('/communities/{id}', [CommunityController::class, 'update']);
Route::patch('/communities/{id}', [CommunityController::class, 'update']); // Optional for partial updates

// Delete a community by ID
Route::delete('/communities/{id}', [CommunityController::class, 'destroy']);
// Get all categories
Route::get('/categories', [CategoryController::class, 'index']);

// Create a new category
Route::post('/categories', [CategoryController::class, 'store']);

// Get a single category by ID
Route::get('/categoriesNEW', [CategoryController::class, 'show']);

// Update a category by ID
Route::post('/categories/{id}', [CategoryController::class, 'update']);

// Delete a category by ID
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);




Route::get('/subcategoriesfetchAll', [SubCategoryController::class, 'fetchAll']);
// Get all subcategories
Route::get('/subcategories', [SubCategoryController::class, 'index']);

// Create a new subcategory
Route::post('/subcategories', [SubCategoryController::class, 'store']);


// Get a single subcategory by ID
Route::get('/subcategories/{subcategory}', [SubCategoryController::class, 'show']);

// Update a subcategory by ID
Route::post('/subcategories/{subcategory}', [SubCategoryController::class, 'update']);

// Delete a subcategory by ID
Route::delete('/subcategories/{subcategory}', [SubCategoryController::class, 'destroy']);
Route::get('/vendor/{slug}', [VendorController::class, 'getVendorBySlug']);
Route::get('/vendor/category/{slug}', [VendorController::class, 'getCategoryDataBySlug']);
Route::get('/vendors/subcategory/{slug}', [VendorController::class, 'getVendorsBySubCategorySlug']);

// Fetch all categories, subcategories, and vendors
Route::get('/vendorsalldata', [VendorController::class, 'getAllData']);

// Fetch all vendors or a single vendor by ID
Route::get('/vendors/{id?}', [VendorController::class, 'fetchVendorDetails']);

// Create a new vendor
Route::post('/vendors', [VendorController::class, 'store']);

// Update a vendor by ID
Route::post('/vendors/{id}', [VendorController::class, 'update']);

// Delete a vendor by ID
Route::delete('/vendors/{id}', [VendorController::class, 'destroy']);
Route::get('/vendors', [VendorController::class, 'getVendorData']);
Route::get('/cities', [VendorController::class, 'getUniqueCities']);
Route::get('/filter-vendors', [VendorController::class, 'filterVendors']);
Route::get('/slug/{category_slug}/{subcategory_slug?}/{vendor_slug?}', [VendorController::class, 'fetchBySlugs']);

// Get all features
Route::get('/features', [FeatureController::class, 'index']);

// Create a new feature
Route::post('/features', [FeatureController::class, 'store']);

// Get a single feature by ID
Route::get('/features/{id}', [FeatureController::class, 'show']);

// Update a feature by ID
Route::put('/features/{id}', [FeatureController::class, 'update']);

// Delete a feature by ID
Route::delete('/features/{id}', [FeatureController::class, 'destroy']);

Route::get('/images', [ImageController::class, 'index']); // Get all images
Route::post('/images', [ImageController::class, 'store']); // Upload an image
Route::get('/images/{id}', [ImageController::class, 'show']); // Get a specific image
Route::delete('/images/{id}', [ImageController::class, 'destroy']); // Delete an image

Route::get('/pricing', [PricingController::class, 'index']);      // Get all pricing records
Route::post('/pricing', [PricingController::class, 'store']);     // Create a new pricing record
Route::get('/pricing/{id}', [PricingController::class, 'show']);  // Get a specific pricing record
Route::put('/pricing/{id}', [PricingController::class, 'update']); // Update an existing pricing record
Route::delete('/pricing/{id}', [PricingController::class, 'destroy']); // Delete a pricing record

Route::get('/reviews', [ReviewController::class, 'index']);

// Create a new review
Route::post('/reviews', [ReviewController::class, 'store']);

// Get a specific review by ID
Route::get('/reviews/{id}', [ReviewController::class, 'show']);

// Update a review by ID
Route::put('/reviews/{id}', [ReviewController::class, 'update']);
Route::patch('/reviews/{id}', [ReviewController::class, 'update']); // Optional: for partial updates

// Delete a review by ID
Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

Route::get('/blogs', [BlogController::class, 'index']);

// Get a single blog by ID
Route::get('/blogs/{id}', [BlogController::class, 'show']);

// Create a new blog
Route::post('/blogs', [BlogController::class, 'store']);

// Update an existing blog
Route::put('/blogs/{id}', [BlogController::class, 'update']);

// Delete a blog
Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);



// Get all ratings
Route::get('/ratings', [RatingController::class, 'index']);

// Get a single rating by ID
Route::get('/ratings/{id}', [RatingController::class, 'show']);

// Create a new rating
Route::post('/ratings', [RatingController::class, 'store']);

// Update a rating by ID
Route::put('/ratings/{id}', [RatingController::class, 'update']);
Route::patch('/ratings/{id}', [RatingController::class, 'update']);

// Delete a rating by ID
Route::delete('/ratings/{id}', [RatingController::class, 'destroy']);
// List all enquiries
Route::get('/enquiries', [EnquiryController::class, 'index']);

// Create a new enquiry
Route::post('/enquiries', [EnquiryController::class, 'store']);

// Get a specific enquiry by ID
Route::get('/enquiries/{id}', [EnquiryController::class, 'show']);

// Update an enquiry by ID
Route::put('/enquiries/{id}', [EnquiryController::class, 'update']);
Route::patch('/enquiries/{id}', [EnquiryController::class, 'update']); // Optional for partial updates

// Delete an enquiry by ID
Route::delete('/enquiries/{id}', [EnquiryController::class, 'destroy']);
// 1. Route to show all groups (the index page)
Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');

// 2. Route to show the form for creating a new group
Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');

// 3. Route to store a new group in the database
Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');

// 4. Route to display a specific group
Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');

// 5. Route to show the form for editing a group
Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');

// 6. Route to update a specific group in the database
Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');

// 7. Route to delete a specific group
Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');