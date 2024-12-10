<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Http\Resources\CommentResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class CommentController extends Controller
{

    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'comment' => ['required', 'string', 'max:500'], // Optional: Limit the length
                'post_id' => ['required', 'integer', 'exists:posts,id'],
            ]);
    
            // Create the comment using validated data and authenticated user ID
            $comment = Comment::create([
                'comment' => $validated['comment'],
                'post_id' => $validated['post_id'],
                'user_id' => auth()->id(),
            ]);
    
            // Return a success response with the created comment
            return response()->json([
                'status' => 'success',
                'message' => 'Comment created successfully!',
                'data' => new CommentResource($comment), // Wrap comment in a resource class
            ], 201); // 201 Created
    
        } catch (ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors occurred.',
                'errors' => $e->errors(),
            ], 422); // 422 Unprocessable Entity
        }
    }

    public function destroy($id)
    {
        // Find the comment by ID
        $comment = Comment::find($id);
    
        // Check if the comment exists
        if (!$comment) {
            return response()->json([
                'message' => 'Comment not found.',
            ], 404); // Return 404 Not Found
        }
    
        // Ensure the authenticated user owns the comment
        if ($comment->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized to delete this comment.',
            ], 403); // Return 403 Forbidden
        }
    
        // Delete the comment with exception handling
        try {
            $comment->delete();
    
            return response()->json([
                'message' => 'Comment deleted successfully!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete the comment.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal Server Error',
            ], 500); // Return 500 Internal Server Error
        }
    }
    
}
