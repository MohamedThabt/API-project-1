<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];

    function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    function comments(){
        return $this->hasMany(Comment::class, 'post_id');
    }

    function likes(){
        return $this->hasMany(Like::class, 'post_id');
    }
}
