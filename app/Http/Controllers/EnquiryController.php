<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    // Store a new enquiry
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'email' => 'required|email|unique:enquiries',
            'description' => 'required|string',
        ]);

        $enquiry = Enquiry::create($request->all());

        return response()->json(['message' => 'Enquiry submitted successfully', 'enquiry' => $enquiry], 201);
    }

    // Get all enquiries
    public function index()
    {
        return response()->json(Enquiry::all());
    }

    // Get a single enquiry
    public function show($id)
    {
        $enquiry = Enquiry::find($id);

        if (!$enquiry) {
            return response()->json(['message' => 'Enquiry not found'], 404);
        }

        return response()->json($enquiry);
    }

    // Update an enquiry
    public function update(Request $request, $id)
    {
        $enquiry = Enquiry::find($id);

        if (!$enquiry) {
            return response()->json(['message' => 'Enquiry not found'], 404);
        }

        $enquiry->update($request->all());

        return response()->json(['message' => 'Enquiry updated successfully', 'enquiry' => $enquiry]);
    }

    // Delete an enquiry
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

