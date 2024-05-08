<?php

namespace App\Utils\Traits;

use App\Models\PostView;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

trait PostTrait
{
    /**
     * @param LengthAwarePaginator $posts
     * @return void
     */
    public function checkIfUserHasViewedPost(LengthAwarePaginator $posts): void
    {
        if (Auth::guard('api')->user()) {
            $user = Auth::guard('api')->user();
            $viewedPostIds = $posts->pluck('id');
            $userViewedPostIds = PostView::where('user_id', $user->getAuthIdentifier())
                ->whereIn('post_id', $viewedPostIds)
                ->pluck('post_id')
                ->toArray();

            // Add a field indicating whether each post has been viewed by the user
            $posts->each(function ($post) use ($userViewedPostIds) {
                $post->setAttribute('userHasViewed', in_array($post->id, $userViewedPostIds));
            });
        }
    }
}
