<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'forum_id',
        'title',
        'description',
        'slug',
    ];

    /**
     * Get replies belonging to the forum
     *
     * @return HasMany
     */
    public function postReplies()
    {
        return $this->hasMany(PostReply::class);
    }

    /**
     * Get tags belonging to the forum
     *
     * @return HasMany
     */
    public function postTags()
    {
        return $this->hasMany(PostTag::class);
    }

    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }
}
