<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Forum extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'avatar_url',
        'category_id',
        'views',
        'threads',
        'messages'
    ];

    /**
     * Get category that owns the forum
     *
     * @return BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the latest post in the forum
     *
     * @return HasOne
     */
    public function latestPost(): HasOne
    {
        return $this->hasOne(Post::class)->latest()->with('user');
    }

    /**
     * Get posts belonging to the forum
     *
     * @return HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function views()
    {
        return $this->morphMany(View::class, 'viewable');
    }

    /**
     * Generate unique slug
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Use the 'creating' event to generate and set the unique slug
        static::creating(function ($forum) {
            $slug = Str::slug($forum->title);
            $uniqueSlug = $slug;

            // Check for uniqueness and append a number if needed
            $counter = 1;
            while (static::where('slug', $uniqueSlug)->exists()) {
                $uniqueSlug = $slug . '-' . $counter;
                $counter++;
            }

            $forum->slug = $uniqueSlug;
        });

        static::updating(function ($forum) {
            // Check if the title has changed
            if ($forum->isDirty('title')) {
                $slug = Str::slug($forum->title);
                $uniqueSlug = $slug;

                // Check for uniqueness and append a number if needed
                $counter = 1;
                while (static::where('slug', $uniqueSlug)->where('id', '!=', $forum->id)->exists()) {
                    $uniqueSlug = $slug . '-' . $counter;
                    $counter++;
                }

                $forum->slug = $uniqueSlug;
            }
        });
    }
}
