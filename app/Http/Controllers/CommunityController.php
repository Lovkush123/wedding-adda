<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CommunityController extends Controller
{
    /**
     * Display a listing of the communities.
     */
    public function index()
    {
        $communities = Community::all()->map(function ($community) {
            $community->image_url = $community->image ? url($community->image) : null;
            return $community;
        });

        return response()->json($communities);
    }

    /**
     * Store a newly created community in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:communities,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
        ]);

        $slug = $request->slug ?? Str::slug($request->name);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('communities', 'public');
            $validated['image'] = 'storage/' . $imagePath;
        }

        $validated['slug'] = $slug;

        $community = Community::create($validated);
        $community->image_url = $community->image ? url($community->image) : null;

        return response()->json([
            'message' => 'Community created successfully.',
            'data' => $community,
        ], 201);
    }

    /**
     * Display the specified community.
     */
    public function show($id)
    {
        $community = Community::find($id);
        if (!$community) {
            return response()->json(['message' => 'Community not found'], 404);
        }

        $community->image_url = $community->image ? url($community->image) : null;
        return response()->json($community);
    }

    /**
     * Update the specified community in storage.
     */
    public function update(Request $request, $id)
    {
        $community = Community::find($id);
        if (!$community) {
            return response()->json(['message' => 'Community not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|unique:communities,slug,' . $id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($community->image && Storage::disk('public')->exists(str_replace('storage/', '', $community->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $community->image));
            }

            $imagePath = $request->file('image')->store('communities', 'public');
            $validated['image'] = 'storage/' . $imagePath;
        }

        $validated['slug'] = $request->slug ?? Str::slug($request->name ?? $community->name);

        $community->update($validated);
        $community->image_url = $community->image ? url($community->image) : null;

        return response()->json([
            'message' => 'Community updated successfully.',
            'data' => $community,
        ]);
    }

    /**
     * Remove the specified community from storage.
     */
    public function destroy($id)
    {
        $community = Community::find($id);
        if (!$community) {
            return response()->json(['message' => 'Community not found'], 404);
        }

        if ($community->image && Storage::disk('public')->exists(str_replace('storage/', '', $community->image))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $community->image));
        }

        $community->delete();

        return response()->json(['message' => 'Community deleted successfully']);
    }
}
