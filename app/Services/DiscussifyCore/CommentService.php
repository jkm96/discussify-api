<?php

namespace App\Services\DiscussifyCore;

use App\Http\Resources\CommentResource;
use App\Models\Post;
use App\Models\Comment;
use App\Models\PostReply;
use App\Models\User;
use App\Utils\Helpers\ModelCrudHelpers;
use App\Utils\Helpers\ResponseHelpers;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    /**
     * @param $commentRequest
     * @return JsonResponse
     */
    public function addNewComment($commentRequest): JsonResponse
    {
        try {
            $user = User::findOrFail(Auth::id());
            $post = PostReply::findOrFail($commentRequest['post_reply_id']);

            $comment = Comment::create([
                'user_id' => $user->id,
                'post_reply_id' => $post->id,
                'description' => $commentRequest['description']
            ]);

            $comment->load('user');

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new CommentResource($comment),
                'Comment created successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error creating comment',
                500
            );
        }
    }

    /**
     * @param $commentId
     * @param $updateRequest
     * @return JsonResponse
     */
    public function editComment($commentId, $updateRequest): JsonResponse
    {
        try {
            PostReply::findOrFail($updateRequest['post_reply_id']);
            $comment = Comment::findOrFail($commentId);

            // Check if the authenticated user owns the post reply
            if ($comment->user_id !== Auth::id()) {
                return ResponseHelpers::ConvertToJsonResponseWrapper(
                    ['error' => 'Unauthorized resource edit'],
                    'You are not authorized to edit this resource',
                    403
                );
            }

            $comment->description = trim($updateRequest['description']);
            $comment->save();

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new CommentResource($comment),
                'Comment edited successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error editing comment',
                500
            );
        }
    }

    /**
     * @param $postReplyId
     * @param $queryParams
     * @return JsonResponse
     */
    public function getPostReplies($postReplyId, $queryParams): JsonResponse
    {
        try {
            $postReply = PostReply::findOrFail($postReplyId);

            $sortBy = Arr::get($queryParams, 'sort_by', 'latest'); // Default to 'latest' if not provided
            $orderBy = $sortBy === 'oldest' ? 'asc' : 'desc';
            $commentsQuery = $postReply->comments()
                ->orderBy('created_at', $orderBy);;

            $pageSize = $userQueryParams['page_size'] ?? 5;
            $currentPage = $userQueryParams['page_number'] ?? 1;

            $comments = $commentsQuery->with('user')->paginate($pageSize, ['*'], 'page', $currentPage);
            $commentsCollection = CommentResource::collection($comments->items());

            return ResponseHelpers::ConvertToPagedJsonResponseWrapper(
                $commentsCollection,
                'Comments retrieved successfully',
                200,
                $comments
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error retrieving comments',
                500
            );
        }
    }
}
