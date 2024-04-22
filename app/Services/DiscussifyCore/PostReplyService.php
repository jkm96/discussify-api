<?php

namespace App\Services\DiscussifyCore;

use App\Http\Requests\Posts\FetchPostsRequest;
use App\Http\Resources\PostReplyResource;
use App\Models\Post;
use App\Models\PostReply;
use App\Models\User;
use App\Utils\Helpers\ModelCrudHelpers;
use App\Utils\Helpers\ResponseHelpers;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class PostReplyService
{
    /**
     * @param $postReplyRequest
     * @return JsonResponse
     */
    public function addNewPostReply($postReplyRequest): JsonResponse
    {
        try {
            $user = User::findOrFail(auth()->user()->getAuthIdentifier());
            $post = Post::findOrFail($postReplyRequest['post_id']);

            $postReply = PostReply::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'description' => $postReplyRequest['description']
            ]);

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new PostReplyResource($postReply),
                'Post reply created successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error creating post reply',
                500
            );
        }
    }

    /**
     * @param $postReplyId
     * @param $updateRequest
     * @return JsonResponse
     */
    public function editPostReply($postReplyId, $updateRequest): JsonResponse
    {
        try {
            $postReply = PostReply::findOrFail($postReplyId);

            $postReply->description = $updateRequest['description'];

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new PostReplyResource($postReply),
                'Post reply updated successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error creating post reply',
                500
            );
        }
    }

    public function getPostReplies($postId, $queryParams)
    {
        try {
            $post = Post::findOrFail($postId);
            $sortBy = $queryParams['sort_by'];//oldest first, newest first
            $postRepliesQuery = $post->postReplies()
                ->orderBy('created_at', 'desc');;

            $pageSize = $userQueryParams['page_size'] ?? 10;
            $currentPage = $userQueryParams['page_number'] ?? 1;
            $postReplies = $postRepliesQuery->paginate($pageSize, ['*'], 'page', $currentPage);

            return ResponseHelpers::ConvertToPagedJsonResponseWrapper(
                PostReplyResource::collection($postReplies->items()),
                'Post replies retrieved successfully',
                200,
                $postReplies
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error retrieving post replies',
                500
            );
        }
    }
}
