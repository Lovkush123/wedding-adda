<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display a listing of the gallery images.
     */
    public function index()
    {
        $galleries = Gallery::with('user')->get();
        return response()->json($galleries);
    }

    /**
     * Store a newly created gallery image.
     */
    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'alt' => 'nullable|string',
            'user_id' => 'required|integer', // Accepts user_id directly
        ]);
    
        $path = $request->file('images')->store('gallery', 'public');
    
        $gallery = Gallery::create([
            'images' => $path,
            'user_id' => $request->user_id, // Directly inserting user_id
            'alt' => $request->alt,
        ]);
    
        return response()->json($gallery, 201);
    }
    
    /**
     * Display the specified gallery image.
     */
    public function show(Gallery $gallery)
    {
        return response()->json($gallery);
    }

    /**
     * Update the specified gallery image.
     */
    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'images' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'alt' => 'nullable|string',
        ]);

        if ($request->hasFile('images')) {
            Storage::disk('public')->delete($gallery->images);
            $path = $request->file('images')->store('gallery', 'public');
            $gallery->images = $path;
        }

        if ($request->filled('alt')) {
            $gallery->alt = $request->alt;
        }

        $gallery->save();

        return response()->json($gallery);
    }

    /**
     * Remove the specified gallery image.
     */
    public function destroy(Gallery $gallery)
    {
        Storage::disk('public')->delete($gallery->images);
        $gallery->delete();
        return response()->json(['message' => 'Gallery image deleted successfully.']);
    }
}
