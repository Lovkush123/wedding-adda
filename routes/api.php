<?php
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

// Example: Auth Routes
Route::prefix('auth')->group(function () {
    Route::post('login', [UserController::class, 'login']);
});

Route::apiResource('users', UserController::class);
Route::apiResource('addresses', AddressController::class);
Route::apiResource('services', ServiceController::class);
Route::apiResource('gallery', GalleryController::class);
Route::apiResource('communities', CommunityController::class)->except(['update']);
Route::post('communities/{id}', [CommunityController::class, 'update']);
Route::patch('communities/{id}', [CommunityController::class, 'update']);
Route::apiResource('categories', CategoryController::class)->except(['show']);
Route::get('/categoriesNEW', [CategoryController::class, 'show']); // Custom show


// Vendor Routes
Route::prefix('vendors')->group(function () {
    Route::get('/', [VendorController::class, 'index']);
    Route::post('/', [VendorController::class, 'store']);
    Route::post('/{id}', [VendorController::class, 'update']);
    Route::delete('/{id}', [VendorController::class, 'destroy']);
    Route::get('/{id?}', [VendorController::class, 'fetchVendorDetails']);
    Route::get('/subcategory/{slug}', [VendorController::class, 'getVendorsBySubCategorySlug']);
    Route::get('/category/{slug}', [VendorController::class, 'getCategoryDataBySlug']);
    Route::get('/alldata', [VendorController::class, 'getAllData']);
    Route::get('/data', [VendorController::class, 'getVendorData']);
    Route::get('/cities', [VendorController::class, 'getUniqueCities']);
    Route::get('/filter', [VendorController::class, 'filterVendors']);
    Route::get('/{slug}', [VendorController::class, 'getVendorBySlug']);
});

// Slug-based Route
Route::get('/slug/{category_slug}/{subcategory_slug?}/{vendor_slug?}', [VendorController::class, 'fetchBySlugs']);

// Subcategories
Route::get('/subcategoriesfetchAll', [SubCategoryController::class, 'fetchAll']);
Route::apiResource('subcategories', SubCategoryController::class)->except(['update']);
Route::post('/subcategories/{subcategory}', [SubCategoryController::class, 'update']);

Route::apiResource('features', FeatureController::class);
Route::apiResource('images', ImageController::class);
Route::apiResource('pricing', PricingController::class);
Route::apiResource('reviews', ReviewController::class);
Route::apiResource('blogs', BlogController::class);
Route::apiResource('ratings', RatingController::class);
Route::apiResource('enquiries', EnquiryController::class);

Route::prefix('groups')->name('groups.')->group(function () {
    Route::get('/', [GroupController::class, 'index'])->name('index');
    Route::get('/create', [GroupController::class, 'create'])->name('create');
    Route::post('/', [GroupController::class, 'store'])->name('store');
    Route::get('/{group}', [GroupController::class, 'show'])->name('show');
    Route::get('/{group}/edit', [GroupController::class, 'edit'])->name('edit');
    Route::put('/{group}', [GroupController::class, 'update'])->name('update');
    Route::delete('/{group}', [GroupController::class, 'destroy'])->name('destroy');
});
