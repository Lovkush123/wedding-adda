<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Display a list of reviews
    public function index()
    {
        $reviews = Review::with('vendor')->latest()->get();
        return response()->json($reviews);
    }

    // Store a new review
    public function store(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'stars' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);
    
        // Create a new review using mass assignment
        $review = Review::create([
            'vendor_id' => $request->vendor_id,
            'stars' => $request->stars,
            'review' => $request->review,
        ]);
    
        // Return a success response with the review data
        return response()->json([
            'message' => 'Review created successfully',
            'review' => $review,
        ], 201);
    }
    

    // Show a specific review
    public function show($id)
    {
        $review = Review::with('vendor')->findOrFail($id);
        return response()->json($review);
    }

    // Update an existing review
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $request->validate([
            'stars' => 'sometimes|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $review->update($request->only('stars', 'review'));

        return response()->json([
            'message' => 'Review updated successfully',
            'review' => $review,
        ]);
    }

    // Delete a review
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Review deleted successfully']);
    }
}
