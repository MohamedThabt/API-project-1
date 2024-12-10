<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    // Add like
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'post_id' => ['required', 'integer', 'exists:posts,id'],
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors occurred.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if user already liked the post
        $userLikePostBefore = Like::where('user_id', auth()->id())
            ->where('post_id', $request->post_id)
            ->first();

        if ($userLikePostBefore) {
            return response()->json([
                'message' => 'You already liked this post',
            ], 400);
        }

        $validated = $validator->validated();

        // Create the like using validated data and authenticated user ID
        $like = Like::create([
            'post_id' => $validated['post_id'],
            'user_id' => auth()->id(),
        ]);

        // Return a success response with the created like
        return response()->json([
            'like_id' => $like->id,
            'message' => 'Like created successfully!',
        ], 201);
    }

    // Delete like
    public function destroy($id)
    {
        $like = Like::find($id);

        if (!$like) {
            return response()->json([
                'message' => 'Like not found',
            ], 404);
        }

        $like->delete();

        return response()->json([
            'message' => 'Like deleted successfully!',
        ], 200);
    }
}
