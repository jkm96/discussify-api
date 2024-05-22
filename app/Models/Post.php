<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

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
        'tags',
        'likes',
        'views',
        'is_system',
    ];

    protected static function boot()
    {
        parent::boot();

        // Use the 'creating' event to generate and set the unique slug
        static::creating(function ($post) {
            $slug = Str::slug($post->title);
            $uniqueSlug = $slug;

            // Check for uniqueness and append a number if needed
            $counter = 1;
            while (static::where('slug', $uniqueSlug)->exists()) {
                $uniqueSlug = $slug . '-' . $counter;
                $counter++;
            }

            $post->slug = $uniqueSlug;
        });
    }

    /**
     * Get the user associated with this post
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get post replies belonging to the post
     *
     * @return HasMany
     */
    public function postReplies()
    {
        return $this->hasMany(PostReply::class);
    }

    /**
     * Get comments belonging to the post
     *
     * @return HasManyThrough
     */
    public function comments()
    {
        return $this->hasManyThrough(Comment::class, PostReply::class);
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

    /**
     * Get the forum associated with this post
     */
    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }

    public function postLikes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function views()
    {
        return $this->morphMany(View::class, 'viewable');
    }
}
