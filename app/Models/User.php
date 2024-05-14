<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'is_active',
        'profile_cover_url',
        'profile_url',
        'posts_count',
        'post_replies_count',
        'comments_count',
        'points_earned',
        'reaction_score',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function following()
    {
        return $this->morphToMany(User::class, 'followable', 'follows', 'user_id', 'followable_id');
    }

    public function followers()
    {
        return $this->morphedByMany(User::class, 'followable', 'follows', 'followable_id', 'user_id');
    }

    /**
     * Get posts belonging to the user
     *
     * @return HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get post replies belonging to the user
     *
     * @return HasMany
     */
    public function postReplies()
    {
        return $this->hasMany(PostReply::class);
    }

    /**
     * Get comments belonging to the user
     *
     * @return HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
