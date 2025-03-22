<?php

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\EnquiryController;
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


Route::get('/services', [ServiceController::class, 'index']); // Get all services
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


// Get all enquiries
Route::get('/enquiries', [EnquiryController::class, 'index']);

// Create a new enquiry
Route::post('/enquiries', [EnquiryController::class, 'store']);

// Get a single enquiry
Route::get('/enquiries/{enquiry}', [EnquiryController::class, 'show']);

// Update an enquiry
Route::put('/enquiries/{enquiry}', [EnquiryController::class, 'update']);

// Delete an enquiry
Route::delete('/enquiries/{enquiry}', [EnquiryController::class, 'destroy']);