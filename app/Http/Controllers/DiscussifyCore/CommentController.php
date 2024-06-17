<?php

namespace App\Http\Controllers\DiscussifyCore;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comments\CreateCommentRequest;
use App\Http\Requests\Comments\UpdateCommentRequest;
use App\Http\Requests\Comments\UpsertReplyRequest;
use App\Http\Requests\PostReplies\FetchPostRepliesFormRequest;
use App\Http\Requests\Posts\FetchPostsFormRequest;
use App\Http\Requests\Posts\UpdatePostRequest;
use App\Services\DiscussifyCore\CommentService;
use App\Services\DiscussifyCore\PostService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    private CommentService $_commentService;

    public function __construct(CommentService $commentService)
    {
        $this->_commentService = $commentService;
    }

    /**
     * @param CreateCommentRequest $commentRequest
     * @return JsonResponse
     */
    public function createComment(CreateCommentRequest $commentRequest): JsonResponse
    {
        return $this->_commentService->addNewComment($commentRequest);
    }

    /**
     * @param $commentId
     * @param UpdateCommentRequest $updateRequest
     * @return JsonResponse
     */
    public function updateComment($commentId,UpdateCommentRequest $updateRequest): JsonResponse
    {
        return $this->_commentService->editComment($commentId,$updateRequest);
    }

    /**
     * @param UpsertReplyRequest $upsertReplyRequest
     * @return JsonResponse
     */
    public function upsertReply(UpsertReplyRequest $upsertReplyRequest): JsonResponse
    {
        return $this->_commentService->upsertReply($upsertReplyRequest);
    }

    /**
     * @param $postReplyId
     * @param FetchPostRepliesFormRequest $postsRequest
     * @return JsonResponse
     */
    public function getComments($postReplyId, FetchPostRepliesFormRequest $postsRequest): JsonResponse
    {
        return $this->_commentService->getPostReplyComments($postReplyId,$postsRequest);
    }

    public function getNestedReplies($commentId, FetchPostRepliesFormRequest $repliesFormRequest): JsonResponse
    {
        return $this->_commentService->getNestedCommentReplies($commentId,$repliesFormRequest);
    }
}
