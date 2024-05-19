<?php

namespace App\Services\DiscussifyCore;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ForumResource;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Forum;
use App\Models\User;
use App\Utils\Helpers\AuthHelpers;
use App\Utils\Helpers\ModelCrudHelpers;
use App\Utils\Helpers\ResponseHelpers;
use App\Utils\Traits\PostTrait;
use App\Utils\Traits\RecordFilterTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ForumService
{
    use RecordFilterTrait;
    use PostTrait;

    /**
     * @param $createForumRequest
     * @return JsonResponse
     */
    public function addNewForum($createForumRequest): JsonResponse
    {
        try {
            Category::where('id', $createForumRequest['category_id'])
                ->firstOrFail();
            $title = trim($createForumRequest['title']);
            $avatarUrl = AuthHelpers::createUserAvatarFromName($title, false);
            $forum = Forum::create([
                'title' => $title,
                'description' => trim($createForumRequest['description']),
                'category_id' => $createForumRequest['category_id'],
                'avatar_url' => $avatarUrl
            ]);

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new ForumResource($forum),
                'Forum created successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error creating forum ',
                500
            );
        }
    }

    /**
     * @param $forumId
     * @param $editForumRequest
     * @return JsonResponse
     */
    public function updateForum($forumId, $editForumRequest): JsonResponse
    {
        try {
            $forum = Forum::where('id', $forumId)
                ->firstOrFail();

            $forum->title = trim($editForumRequest['title']);
            $forum->description = trim($editForumRequest['description']);
            $forum->save();

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new ForumResource($forum),
                'Forum updated successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error creating forum ',
                500
            );
        }
    }

    /**
     * @return JsonResponse
     */
    public function getForums(): JsonResponse
    {
        try {
            $forums = Forum::get();

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ForumResource::collection($forums),
                'Forums retrieved successfully',
                200
            );
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error retrieving forums',
                500
            );
        }
    }

    /**
     * @param $slug
     * @param $fetchPostsRequest
     * @return JsonResponse
     */
    public function getForumPosts($slug, $fetchPostsRequest): JsonResponse
    {
        try {
            $forum = Forum::where('slug', $slug)->firstOrFail();

            $postsQuery = $forum->posts()
                ->withCount('postReplies')
                ->orderByDesc('created_at');

            $this->applyPostFilters($postsQuery, $fetchPostsRequest);

            $pageSize = $fetchPostsRequest['page_size'] ?? 10;
            $currentPage = $fetchPostsRequest['page_number'] ?? 1;
            $posts = $postsQuery->paginate($pageSize, ['*'], 'page', $currentPage);
            $posts->getCollection()->load('user');

            $posts->getCollection()->each(function ($post) {
                $lastReply = $post->postReplies()->latest()->first();
                if ($lastReply) {
                    $post->setAttribute('last_reply_user', $lastReply->user);
                    $post->setAttribute('last_reply_created_at', $lastReply->created_at);
                } else {
                    $post->setAttribute('last_reply_user', null);
                    $post->setAttribute('last_reply_created_at', null);
                }
            });

            $this->checkIfUserHasViewedPostOrFollowedPostAuthor($posts);

            $response = [
                'forum' => new ForumResource($forum),
                'posts' => PostResource::collection($posts->items())
            ];

            return ResponseHelpers::ConvertToPagedJsonResponseWrapper(
                $response,
                'Posts retrieved successfully for forum: ' . $forum->title,
                200,
                $posts
            );
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error retrieving forum posts',
                500
            );
        }
    }

    /**
     * @param $slug
     * @return JsonResponse
     */
    public function getForumBySlug($slug): JsonResponse
    {
        try {
            $forum = Forum::where('slug', $slug)
                ->firstOrFail();

            if (Auth::guard('api')->user()) {
                $userId = Auth::guard('api')->id();
                if (!$forum->views()->where('user_id', $userId)->exists()) {
                    $forum->views()->create([
                        'user_id' => $userId,
                        'viewable_id' => $forum->id,
                        'viewable_type' => get_class($forum),
                    ]);
                    $forum->increment('views');
                }
            }

            $forum->views += 1;
            $forum->save();

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new ForumResource($forum),
                'Forum retrieved successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error retrieving forum',
                500
            );
        }
    }
}
