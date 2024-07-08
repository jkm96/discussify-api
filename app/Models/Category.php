<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'slug',
        'threads',
        'messages',
        'views',
    ];

    /**
     * @return HasMany
     */
    public function forums(): HasMany
    {
        return $this->hasMany(Forum::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Use the 'creating' event to generate and set the unique slug
        static::creating(function ($category) {
            $slug = Str::slug($category->name);
            $uniqueSlug = $slug;

            // Check for uniqueness and append a number if needed
            $counter = 1;
            while (static::where('slug', $uniqueSlug)->exists()) {
                $uniqueSlug = $slug . '-' . $counter;
                $counter++;
            }

            $category->slug = $uniqueSlug;
        });
    }
}
