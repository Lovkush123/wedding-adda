<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    /**
     * Display a listing of the enquiries.
     */
    public function index()
    {
        $enquiries = Enquiry::all();
        return response()->json($enquiries);
    }

    /**
     * Store a newly created enquiry in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string',
        ]);

        $enquiry = Enquiry::create($request->all());
        return response()->json($enquiry, 201);
    }

    /**
     * Display the specified enquiry.
     */
    public function show(Enquiry $enquiry)
    {
        return response()->json($enquiry);
    }

    /**
     * Update the specified enquiry in storage.
     */
    public function update(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'number' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|max:255',
            'description' => 'nullable|string',
        ]);

        $enquiry->update($request->all());
        return response()->json($enquiry);
    }

    /**
     * Remove the specified enquiry from storage.
     */
    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();
        return response()->json(['message' => 'Enquiry deleted successfully']);
    }
}
