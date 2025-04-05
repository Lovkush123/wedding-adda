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
use Illuminate\Support\Facades\Route;

Route::get('/users', [UserController::class, 'index']); // Get all users
Route::post('/users', [UserController::class, 'store']); // Create a user
Route::get('/users/{id}', [UserController::class, 'show']); // Get a single user
Route::put('/users/{id}', [UserController::class, 'update']); // Update a user
Route::delete('/users/{id}', [UserController::class, 'destroy']); // Delete a user


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


// Create an enquiry
Route::post('/enquiries', [EnquiryController::class, 'store']);

// Get all enquiries
Route::get('/enquiries', [EnquiryController::class, 'index']);

// Get a single enquiry
Route::get('/enquiries/{id}', [EnquiryController::class, 'show']);

// Update an enquiry
Route::put('/enquiries/{id}', [EnquiryController::class, 'update']);

// Delete an enquiry
Route::delete('/enquiries/{id}', [EnquiryController::class, 'destroy']);



// Get all categories
Route::get('/categories', [CategoryController::class, 'index']);

// Create a new category
Route::post('/categories', [CategoryController::class, 'store']);

// Get a single category by ID
Route::get('/categoriesNEW', [CategoryController::class, 'show']);

// Update a category by ID
Route::put('/categories/{id}', [CategoryController::class, 'update']);

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
Route::put('/subcategories/{subcategory}', [SubCategoryController::class, 'update']);

// Delete a subcategory by ID
Route::delete('/subcategories/{subcategory}', [SubCategoryController::class, 'destroy']);


// Fetch all categories, subcategories, and vendors
Route::get('/vendorsalldata', [VendorController::class, 'getAllData']);

// Fetch all vendors or a single vendor by ID
Route::get('/vendors/{id?}', [VendorController::class, 'fetchVendorDetails']);

// Create a new vendor
Route::post('/vendors', [VendorController::class, 'store']);

// Update a vendor by ID
Route::put('/vendors/{id}', [VendorController::class, 'update']);

// Delete a vendor by ID
Route::delete('/vendors/{id}', [VendorController::class, 'destroy']);

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