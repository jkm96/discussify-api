<?php

namespace App\Services\DiscussifyCore;

use App\Http\Requests\Posts\FetchPostsFormRequest;
use App\Http\Resources\PostReplyResource;
use App\Models\Post;
use App\Models\PostReply;
use App\Models\User;
use App\Utils\Helpers\ModelCrudHelpers;
use App\Utils\Helpers\ResponseHelpers;
use App\Utils\Traits\PostTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class PostReplyService
{
    use PostTrait;
    /**
     * @param $postReplyRequest
     * @return JsonResponse
     */
    public function addNewPostReply($postReplyRequest): JsonResponse
    {
        try {
            $user = User::findOrFail(Auth::id());
            $post = Post::findOrFail($postReplyRequest['post_id']);

            $postReply = PostReply::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'description' => $postReplyRequest['description']
            ]);

            $postReply->load('user');

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

            // Check if the authenticated user owns the post reply
            if ($postReply->user_id !== Auth::id()) {
                return ResponseHelpers::ConvertToJsonResponseWrapper(
                    ['error' => 'Unauthorized resource edit'],
                    'You are not authorized to edit this resource',
                    403
                );
            }

            $postReply->description = $updateRequest['description'];
            $postReply->save();

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

    /**
     * @param $postSlug
     * @param $queryParams
     * @return JsonResponse
     */
    public function getPostReplies($postSlug, $queryParams): JsonResponse
    {
        try {
            $post = Post::where('slug',$postSlug)->firstOrFail();
            // Extract the sort_by parameter from $queryParams
            $sortBy = Arr::get($queryParams, 'sort_by', 'latest'); // Default to 'latest' if not provided
            $orderBy = $sortBy === 'oldest' ? 'asc' : 'desc';

            $postRepliesQuery = $post->postReplies()->orderBy('created_at', $orderBy);

            $pageSize = Arr::get($queryParams, 'page_size', 10);
            $currentPage = Arr::get($queryParams, 'page_number', 1);

            $postReplies = $postRepliesQuery
                ->with('user')
                ->withCount('comments')
                ->paginate($pageSize, ['*'], 'page', $currentPage);

            $this->checkIfUserHasFollowedRecordAuthor($postReplies);

            $postRepliesCollection = PostReplyResource::collection($postReplies->items());

            return ResponseHelpers::ConvertToPagedJsonResponseWrapper(
                $postRepliesCollection,
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
