<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostReply extends Model
{
    use HasFactory;

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function parentReply()
    {
        return $this->belongsTo(Reply::class, 'parent_reply_id');
    }

    public function childReplies()
    {
        return $this->hasMany(Reply::class, 'parent_reply_id');
    }
}
