<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Comment;
use App\Models\Like;
use App\Models\User;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "title"=>$this->title,
            "content"=>$this->content,
            "author"=>UserResource::make( User::find($this->user_id)),
            "published_at"=>$this->created_at,
            "last_updated"=>$this->updated_at,
            "comment_count"=>Comment::where('post_id', $this->id)->count(),
            "comments"=>CommentResource::collection(Comment::where('post_id', $this->id)->get()),
            "likes_count"=>Like::where('post_id', $this->id)->count(),

        ];
    }
}
