<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    /**
     * Display a listing of the groups.
     */
    public function index()
    {
        $groups = Group::all()->map(function ($group) {
            $group->image_url = $group->image ? url($group->image) : null;
            return $group;
        });

        return response()->json($groups);
    }

    /**
     * Store a newly created group in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|unique:groups,slug',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
            'type'        => 'nullable|string|max:100',
        ]);

        $validated['slug'] = $request->slug ?? Str::slug($request->name);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('groups', 'public');
            $validated['image'] = 'storage/' . $path;
        }

        $group = Group::create($validated);
        $group->image_url = $group->image ? url($group->image) : null;

        return response()->json([
            'message' => 'Group created successfully.',
            'data'    => $group,
        ], 201);
    }

    /**
     * Display the specified group.
     */
    public function show($id)
    {
        $group = Group::find($id);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $group->image_url = $group->image ? url($group->image) : null;
        return response()->json($group);
    }

    /**
     * Update the specified group in storage.
     */
    public function update(Request $request, $id)
    {
        $group = Group::find($id);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'slug'        => 'nullable|string|unique:groups,slug,' . $id,
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
            'type'        => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('image')) {
            if ($group->image && Storage::disk('public')->exists(str_replace('storage/', '', $group->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $group->image));
            }

            $path = $request->file('image')->store('groups', 'public');
            $validated['image'] = 'storage/' . $path;
        }

        $validated['slug'] = $request->slug ?? Str::slug($request->name ?? $group->name);

        $group->update($validated);
        $group->image_url = $group->image ? url($group->image) : null;

        return response()->json([
            'message' => 'Group updated successfully.',
            'data'    => $group,
        ]);
    }

    /**
     * Remove the specified group from storage.
     */
    public function destroy($id)
    {
        $group = Group::find($id);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        if ($group->image && Storage::disk('public')->exists(str_replace('storage/', '', $group->image))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $group->image));
        }

        $group->delete();

        return response()->json(['message' => 'Group deleted successfully.']);
    }
}
