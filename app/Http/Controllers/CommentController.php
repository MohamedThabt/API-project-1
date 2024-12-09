<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Comment; 
use \Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{

    public function postComment(Request $request)
{
    // Validate the incoming request data
    $validator = Validator::make($request->all(), [
        'comment' => ['required', 'string'],
        'post_id' => ['required', 'integer', 'exists:posts,id'],
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation errors occurred.',
            'errors' => $validator->errors(),
        ], 422);
    }
    $validated = $validator->validated();

   
    
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

}
