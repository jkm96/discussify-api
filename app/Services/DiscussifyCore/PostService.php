<?php

namespace App\Services\DiscussifyCore;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Forum;
use App\Models\Post;
use App\Models\PostTag;
use App\Models\User;
use App\Utils\Helpers\AuthHelpers;
use App\Utils\Helpers\ModelCrudHelpers;
use App\Utils\Helpers\ResponseHelpers;
use App\Utils\Traits\DateFilterTrait;
use App\Utils\Traits\RecordFilterTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostService
{
    use RecordFilterTrait;

    /**
     * @param $createPostRequest
     * @return JsonResponse
     */
    public function addNewPost($createPostRequest): JsonResponse
    {
        try {
            $forum = Forum::where('slug', $createPostRequest['forum_slug'])
                ->firstOrFail();

            $user = User::findOrFail(auth()->user()->getAuthIdentifier());

            $post = Post::create([
                'title' => trim($createPostRequest['title']),
                'description' => trim($createPostRequest['description']),
                'forum_id' => $forum->id,
                'user_id' => $user->id,
                'tags' => $createPostRequest['tags'] ? trim($createPostRequest['tags']) : ''
            ]);

            if (isset($createPostRequest['tags'])){
                $tags = trim($createPostRequest['tags']);
                $tagsArray = explode(',', $tags);
                $tagsArray = array_map('trim', $tagsArray);
                foreach ($tagsArray as $tag) {
                    PostTag::create([
                        'post_id' => $post->id,
                        'tag' => $tag
                    ]);
                }
            }

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new PostResource($post),
                'Post created successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error creating Post ',
                500
            );
        }
    }

    /**
     * @param $postId
     * @param $updatePostRequest
     * @return JsonResponse
     */
    public function editPost($postId, $updatePostRequest): JsonResponse
    {
        try {
            $post = Post::findOrFail($postId);

            // Check if the authenticated user owns the post
            if ($post->user_id !== Auth::id()) {
                return ResponseHelpers::ConvertToJsonResponseWrapper(
                    null,
                    'You are not authorized to edit this post',
                    403
                );
            }

            DB::beginTransaction();

            // Update post attributes based on request data
            $post->title = trim($updatePostRequest['title']);
            $post->description = trim($updatePostRequest['description']);
            if (isset($updatePostRequest['forum_id'])) {
                $forumId = $updatePostRequest['forum_id'];
                Forum::findOrFail($forumId);
                $post->forum_id = $forumId;
            }

            if (isset($updatePostRequest['tags'])){
                $post->tags = trim($updatePostRequest['tags']);
                // Update post tags
                $tags = trim($updatePostRequest['tags']);
                $tagsArray = explode(',', $tags);
                $tagsArray = array_map('trim', $tagsArray);

                // Delete existing post tags
                $post->postTags()->delete();

                // Create new post tags
                foreach ($tagsArray as $tag) {
                    PostTag::create([
                        'post_id' => $post->id,
                        'tag' => $tag
                    ]);
                }
            }

            $post->save();

            DB::commit();

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new PostResource($post),
                'Post updated successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error updating Post',
                500
            );
        }
    }

    /**
     * @param $queryParams
     * @return JsonResponse
     */
    public function getPosts($queryParams): JsonResponse
    {
        try {
            $postsQuery = Post::with('user', 'forum')
                ->orderBy('created_at', 'desc');

            $this->applyPostFilters($postsQuery, $queryParams);

            $pageSize = $queryParams['page_size'] ?? 10;
            $currentPage = $queryParams['page_number'] ?? 1;
            $posts = $postsQuery->paginate($pageSize, ['*'], 'page', $currentPage);

            return ResponseHelpers::ConvertToPagedJsonResponseWrapper(
                PostResource::collection($posts->items()),
                'Posts retrieved successfully',
                200,
                $posts
            );
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToPagedJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error retrieving Posts',
                500
            );
        }
    }

    /**
     * @param $slug
     * @return JsonResponse
     */
    public function getPostBySlug($slug): JsonResponse
    {
        try {
            $Post = Post::with('user')
                ->where('slug', $slug)
                ->firstOrFail();

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new PostResource($Post),
                'Post retrieved successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error retrieving post',
                500
            );
        }
    }

    /**
     * @return JsonResponse
     */
    public function getCoverPosts(): JsonResponse
    {
        try {
            $Posts = Post::with('user', 'forum')->get();

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                PostResource::collection($Posts),
                'Posts retrieved successfully',
                200
            );
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error retrieving Posts',
                500
            );
        }
    }
}
