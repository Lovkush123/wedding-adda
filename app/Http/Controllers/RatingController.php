<?php
namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    // List all ratings
    public function index()
    {
        return response()->json(Rating::all());
    }

    // Show a single rating
    public function show($id)
    {
        $rating = Rating::find($id);

        if (!$rating) {
            return response()->json(['message' => 'Rating not found'], 404);
        }

        return response()->json($rating);
    }

    // Store a new rating
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'vender_id' => 'required|integer',
            'vender_type' => 'required|string|max:50',
            'rating_value' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $rating = Rating::create($validated);

        return response()->json($rating, 201);
    }

    // Update a rating
    public function update(Request $request, $id)
    {
        $rating = Rating::find($id);

        if (!$rating) {
            return response()->json(['message' => 'Rating not found'], 404);
        }

        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'vender_id' => 'sometimes|integer',
            'vender_type' => 'sometimes|string|max:50',
            'rating_value' => 'sometimes|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $rating->update($validated);

        return response()->json($rating);
    }

    // Delete a rating
    public function destroy($id)
    {
        $rating = Rating::find($id);

        if (!$rating) {
            return response()->json(['message' => 'Rating not found'], 404);
        }

        $rating->delete();

        return response()->json(['message' => 'Rating deleted']);
    }
}
