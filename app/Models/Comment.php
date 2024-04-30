<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'user_id',
        'post_reply_id',
        'parent_comment_id',
    ];

    public function postReply()
    {
        return $this->belongsTo(PostReply::class, 'post_reply_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class); // Assuming User model exists
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_comment_id');
    }
}
