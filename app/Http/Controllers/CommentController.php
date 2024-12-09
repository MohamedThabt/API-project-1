<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'comment' => ['required', 'string'],
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
            'message' => 'Comment created successfully!',
            'comment' => $comment,
        ], 201);
    }

    public function destroy(Comment $comment)
    {
        // Ensure the authenticated user owns the comment (optional, for added security)
        if ($comment->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized to delete this comment.',
            ], 403);
        }

        // Delete the comment
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully!',
        ], 200);
    }
}
