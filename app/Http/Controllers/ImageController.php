<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    /**
     * Display a listing of the images.
     */
    public function index()
    {
        $images = Image::all();
        return response()->json($images);
    }

    /**
     * Store a newly uploaded image.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'vendor_id' => 'required|exists:vendors,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $path = $request->file('image')->store('images', 'public');

        $image = Image::create([
            'image' => $path,
            'vendor_id' => $request->vendor_id,
        ]);

        return response()->json($image, 201);
    }

    /**
     * Display the specified image.
     */
    public function show($id)
    {
        $image = Image::find($id);
        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }
        return response()->json($image);
    }

    /**
     * Remove the specified image.
     */
    public function destroy($id)
    {
        $image = Image::find($id);
        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        Storage::disk('public')->delete($image->image);
        $image->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }
}
