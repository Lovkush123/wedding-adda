<?php
namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    // Display all blogs
    public function index()
    {
        return response()->json(Blog::all(), 200);
    }

    // Show a single blog
    public function show($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        return response()->json($blog, 200);
    }

    // Create a new blog
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => ['required', function ($attribute, $value, $fail) {
                if (str_word_count(strip_tags($value)) > 1000) {
                    $fail('The content may not be more than 1000 words.');
                }
            }],
            'author' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'status' => 'in:draft,published',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        $blog = Blog::create($validated);

        return response()->json($blog, 201);
    }

    // Update an existing blog
    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => ['sometimes', function ($attribute, $value, $fail) {
                if (str_word_count(strip_tags($value)) > 1000) {
                    $fail('The content may not be more than 1000 words.');
                }
            }],
            'author' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'status' => 'in:draft,published',
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $blog->update($validated);

        return response()->json($blog, 200);
    }

    // Delete a blog
    public function destroy($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        $blog->delete();

        return response()->json(['message' => 'Blog deleted successfully'], 200);
    }
}
