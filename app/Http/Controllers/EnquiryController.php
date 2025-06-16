<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    // Show all enquiries
    public function index()
    {
        return response()->json(Enquiry::all());
    }

    // Store a new enquiry
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'vander_id' => 'required|integer',
            'enquiry_type' => 'required|string|max:100',
            'note' => 'nullable|string',
        ]);

        $enquiry = Enquiry::create($request->all());

        return response()->json([
            'message' => 'Enquiry created successfully',
            'data' => $enquiry,
        ], 201);
    }

    // Show a specific enquiry
    public function show($id)
    {
        $enquiry = Enquiry::find($id);

        if (!$enquiry) {
            return response()->json(['message' => 'Enquiry not found'], 404);
        }

        return response()->json($enquiry);
    }

    // Update a specific enquiry
    public function update(Request $request, $id)
    {
        $enquiry = Enquiry::find($id);

        if (!$enquiry) {
            return response()->json(['message' => 'Enquiry not found'], 404);
        }

        $enquiry->update($request->all());

        return response()->json([
            'message' => 'Enquiry updated successfully',
            'data' => $enquiry,
        ]);
    }

    // Delete a specific enquiry
    public function destroy($id)
    {
        $enquiry = Enquiry::find($id);

        if (!$enquiry) {
            return response()->json(['message' => 'Enquiry not found'], 404);
        }

        $enquiry->delete();

        return response()->json(['message' => 'Enquiry deleted successfully']);
    }
}
