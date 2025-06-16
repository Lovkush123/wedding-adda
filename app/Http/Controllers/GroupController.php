<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Group::latest()->paginate(10);
        // Returns a view, passing all the groups to it
        return view('groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Returns the view with the form to create a group
        return view('groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Example image validation
            'type' => 'nullable|string|max:50',
        ]);

        // Automatically create a slug from the name
        $validatedData['slug'] = Str::slug($validatedData['name']);

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('group_images', 'public');
        }
        
        Group::create($validatedData);

        return redirect()->route('groups.index')->with('success', 'Group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        // Thanks to Route-Model binding, Laravel automatically finds the group by its ID
        return view('groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        return view('groups.edit', compact('group'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'nullable|string|max:50',
        ]);

        // Update slug if the name is changed
        $validatedData['slug'] = Str::slug($validatedData['name']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Optionally, delete the old image first
            // Storage::disk('public')->delete($group->image);
            $validatedData['image'] = $request->file('image')->store('group_images', 'public');
        }

        $group->update($validatedData);

        return redirect()->route('groups.index')->with('success', 'Group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        // Optionally, delete the associated image from storage
        // if ($group->image) {
        //     Storage::disk('public')->delete($group->image);
        // }
        
        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Group deleted successfully.');
    }
}