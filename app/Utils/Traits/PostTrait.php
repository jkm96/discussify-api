<?php

namespace App\Utils\Traits;

use App\Models\Follow;
use App\Models\PostView;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

trait PostTrait
{
    /**
     * @param $posts
     * @return void
     */
    public function checkIfUserHasViewedPostOrFollowedPostAuthor($posts): void
    {
        if (Auth::guard('api')->user()) {
            $user = Auth::guard('api')->user();

            $viewedPostIds = $posts->pluck('id');
            $userViewedPostIds = PostView::where('user_id', $user->getAuthIdentifier())
                ->whereIn('post_id', $viewedPostIds)
                ->pluck('post_id')
                ->toArray();

            $userFollowedAuthor = $this->getUserHasFollowedRecordAuthor($posts, $user);

            $posts->each(function ($post) use ($userViewedPostIds, $userFollowedAuthor) {
                $post->setAttribute('userHasViewed', in_array($post->id, $userViewedPostIds));
                $post->setAttribute('userHasFollowedAuthor', in_array($post->user_id, $userFollowedAuthor));
            });
        }
    }

    public function checkIfUserHasFollowedRecordAuthor($posts): void
    {
        if (Auth::guard('api')->user()) {
            $user = Auth::guard('api')->user();

            $userFollowedAuthor = $this->getUserHasFollowedRecordAuthor($posts, $user);

            // Add a field indicating whether each post has been viewed by the user
            $posts->each(function ($post) use ($userFollowedAuthor) {
                $post->setAttribute('userHasFollowedAuthor', in_array($post->user_id, $userFollowedAuthor));
            });
        }
    }

    /**
     * @param $posts
     * @param Authenticatable|null $user
     * @return mixed
     */
    public function getUserHasFollowedRecordAuthor($posts, $user)
    {
        //check if the current user has followed post authors
        $authorIds = $posts->pluck('user_id')->unique();
        return Follow::where('user_id', $user->id)
            ->whereIn('followable_id', $authorIds)
            ->where('followable_type', 'App\Models\User')
            ->pluck('followable_id')
            ->toArray();
    }
}
