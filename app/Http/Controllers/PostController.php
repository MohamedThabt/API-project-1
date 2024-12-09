<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    // Get all posts
    public function index()
    {
        $posts = Post::paginate(10);
        return response()->json(['posts' => $posts], 200);
    }

    public function store(Request $request)
    {
        // Use Validator to validate the request
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:6'],
            'user_id' => auth()->id(),
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Retrieve validated data
        $validated = $validator->validated();

        // Create the post
        $post = Post::create($validated);

        // Return success response
        return response()->json([
            'message' => 'Post created successfully!',
            'post' => $post,
        ], 201);
    }

    // Edit post
    public function edit(Request $request, Post $post)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:6'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate the user_id to ensure the authenticated user is the post owner (optional security check)
        if ($post->user_id !== $request->user_id) {
            return response()->json([
                'message' => 'Unauthorized to edit this post.',
            ], 403);
        }

        // Update the post
        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // Return success response
        return response()->json([
            'message' => 'Post updated successfully!',
            'post' => $post,
        ], 200);
    }

    public function show(Post $post)
    {
        return response()->json([
            'post' => $post,
        ], 200);
    }

    // Delete a post
    public function destroy(Post $post)
    {
        // Delete the post
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully!',
            'post' => $post,
        ], 200);
    }
}
