<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumStatistics extends Model
{
    use HasFactory;

    protected $fillable = [
        'members',
        'posts',
        'forum_id',
        'forum_name',
        'forum_description'
    ];
}
