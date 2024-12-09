<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{

    // Get all posts
    public function index(){
        $posts = Post::all();
        return response()->json(['posts' => $posts], 200);
    }


    //Add new post
    public function addPost(Request $request){
        $request->validate( [ 
            'title' => ['required','string','max:255'],
            'content' => ['required','string','min:6'],
            'user_id' => ['required','integer','exists:users,id'],
        ]);
        
        try{
            $post = Post::create([
                'title' => $request->title,
                'content' => $request->content,
                'user_id' => $request->user_id,
            ]);
            return response()->json(['message' => 'Post created successfully!',
                                            'post'=> $post], 201); 
        }catch(\Exception $e){
            return response()->json(['message' => 'Post creation failed!'], 500);   
        }
    }

    // Edit post
    public function editPost(Request $request){
        $request->validate( [ 
            'title' => ['required','string','max:255'],
            'content' => ['required','string','min:6'],
            'id' => ['required','integer','exists:posts,id'],
        ]);
        
        try{
            $post = Post::where('id', $request->id)->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
            return response()->json(['message' => 'Post updated successfully!',
                                            'post'=> $post], 201); 
        }catch(\Exception $e){
            return response()->json(['message' => 'Post update failed!'], 500);   
        }
    }

    // get single post
    public function getPost($id)
    {
        $validatedData = ['id' => $id];
        $validator = validator($validatedData, [
            'id' => ['required', 'integer', 'exists:posts,id'],
        ]);
        
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid post ID'], 422);
        }
    
        $post = Post::with('user','comments','likes')->find( $id );
        return response()->json(['post' => $post], 200);
    }
    


    // delete post 
    public function deletePost($id){
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
