<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    // Get all posts
    public function index(){
        $posts = Post::all();
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
    public function edit(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:posts,id'], // Ensure the post exists
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
    
        $validated = $validator->validated();
    
        // Find and update the post
        $post = Post::find($validated['id']);
    
        // Ensure the post belongs to the authenticated user (optional security check)
        if ($post->user_id !== $validated['user_id']) {
            return response()->json([
                'message' => 'Unauthorized to edit this post.',
            ], 403);
        }
    
        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);
    
        // Return success response
        return response()->json([
            'message' => 'Post updated successfully!',
            'post' => $post,
        ], 200);
    }
    


    // Get single post
    public function show($id)
    {
         // Validate the post ID
    $validator = Validator::make(['id' => $id], [
        'id' => ['required', 'integer', 'exists:posts,id'],
    ]);
        
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Invalid post ID',
            'errors' => $validator->errors(),
        ], 422);
    }
     // Retrieve the post with its relationships
    $post = Post::with(['user', 'comments', 'likes'])->find($id);

    return response()->json([
        'message' => 'Post retrieved successfully!',
        'post' => $post,
    ], 200);
    }
    


    // delete post 
    public function destroy($id){
        $validatedData = ['id' => $id];
        $validator = validator($validatedData, [
            'id' => ['required', 'integer', 'exists:posts,id'],
        ]);


        if ($validator->fails()) {
            return response()->json(['message' => 'Post deletion failed!'], 422);
        }
    
        $post = Post::find($id);
        $post = Post::where('id', $id)->delete();
        return response()->json(['message' => 'Post deleted successfully!',
                                        'post'=> $post], 201); 



    }
}
