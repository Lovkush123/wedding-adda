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
public function index(Request $request)
{
    $query = Community::query();

    // Apply search filter
    if ($search = $request->query('search')) {
        $query->where('name', 'like', '%' . $search . '%');
    }

    // Apply type filter
    if (!is_null($request->get('type'))) {
        $query->where('type', $request->get('type'));
    }

    // Get all matched records (no pagination)
    $communities = $query->get();

    // Add full image URL
  $data = $communities->transform(function ($community) {
    $community->image = $community->image 
        ? url('storage/communities/' . basename($community->image))
        : null;
    return $community;
});
    // Return the list of communities
    return response()->json($data);
}



    /**
     * Store a newly created community in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|unique:communities,slug',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
            'type'        => 'nullable|string|max:100',
        ]);

        $validated['slug'] = $request->slug ?? Str::slug($request->name);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('communities', 'public');
            $validated['image'] = 'storage/' . $path;
        }

        $community = Community::create($validated);
        $community->image_url = $community->image ? url($community->image) : null;

        return response()->json([
            'message' => 'Community created successfully.',
            'data'    => $community,
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
            'name'        => 'sometimes|required|string|max:255',
            'slug'        => 'nullable|string|unique:communities,slug,' . $id,
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
            'type'        => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('image')) {
            if ($community->image && Storage::disk('public')->exists(str_replace('storage/', '', $community->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $community->image));
            }

            $path = $request->file('image')->store('communities', 'public');
            $validated['image'] = 'storage/' . $path;
        }

        $validated['slug'] = $request->slug ?? Str::slug($request->name ?? $community->name);

        $community->update($validated);
        $community->image_url = $community->image ? url($community->image) : null;

        return response()->json([
            'message' => 'Community updated successfully.',
            'data'    => $community,
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

        return response()->json(['message' => 'Community deleted successfully.']);
    }
}
