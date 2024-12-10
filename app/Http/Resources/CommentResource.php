<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class CommentResource extends JsonResource
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
            "comment"=>$this->comment,            
            "author"=>UserResource::make( User::find($this->user_id)),
            "published_at"=>$this->created_at,
            "last_updated"=>$this->updated_at,
        ];
    }
}
