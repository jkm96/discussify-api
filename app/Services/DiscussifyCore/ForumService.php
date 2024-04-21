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
use App\Utils\Traits\RecordFilterTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class ForumService
{
    use RecordFilterTrait;

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
     * @return JsonResponse
     */
    public function getForums(): JsonResponse
    {
        try {
            $forums = Forum::with('category')->get();

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
            // Retrieve the forum by its slug
            $forum = Forum::where('slug', $slug)->firstOrFail();

            // Fetch posts related to the forum
            $postsQuery = $forum->posts()->orderBy('created_at', 'desc');

            $this->applyPostFilters($postsQuery, $fetchPostsRequest);

            $pageSize = $userQueryParams['page_size'] ?? 10;
            $currentPage = $userQueryParams['page_number'] ?? 1;
            $posts = $postsQuery->paginate($pageSize, ['*'], 'page', $currentPage);

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
            $forum = Forum::with('posts')
                ->where('slug', $slug)
                ->firstOrFail();

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

    public function getForumStatistics()
    {
    }
}
