<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    // Get all posts
    public function index()
    {
        $posts = Post::paginate(10);
        return response()->json(['posts' => PostResource::collection($posts)], 200);
    }


    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'content' => ['required', 'string', 'min:6'],
            ]);
    
            // Create the post with the authenticated user ID
            $post = Post::create([
                'title' => $validatedData['title'],
                'content' => $validatedData['content'],
                'user_id' => auth()->id(),
            ]);
    
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Post created successfully!',
                'data' => new PostResource($post), // Use a resource for structured data
            ], 201); // 201 Created
    
        } catch (ValidationException $e) {
            // Return validation errors in JSON format
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors occurred.',
                'errors' => $e->errors(),
            ], 422); // 422 Unprocessable Entity
        }
    }
    

    public function edit(Request $request, $id)
    {
        try {
            // Find the post
            $post = Post::find($id);

            if (!$post) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Post not found',
                ], 404); // 404 Not Found
            }

            // Ensure the authenticated user owns the post
            if ($post->user_id !== auth()->id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to edit this post.',
                ], 403); // 403 Forbidden
            }

            // Validate the request data
            $validatedData = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'content' => ['required', 'string', 'min:6'],
            ]);

            // Update the post
            $post->update($validatedData);

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Post updated successfully!',
                'data' => new PostResource($post), // Wrap post in a resource
            ], 200); // 200 OK

        } catch (ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors occurred.',
                'errors' => $e->errors(),
            ], 422); // 422 Unprocessable Entity
        }
    }


    public function show($id)
    {
        $post = Post::find($id);
        
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }
        $post = new PostResource( Post::find($id));
    
        return response()->json([
            'post' => $post,
        ], 200);
    }

    // Delete a post
    public function destroy($id)
    {
        // Find the post
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        // Ensure the authenticated user owns the post
        if ($post->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized to delete this post.',
            ], 403);
        }
    
        // Delete the post
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully!',
            'post' => new PostResource($post),
        ], 200);
    }
}
