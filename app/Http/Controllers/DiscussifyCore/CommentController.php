<?php

namespace App\Http\Controllers\DiscussifyCore;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comments\CreateCommentRequest;
use App\Http\Requests\Comments\UpdateCommentRequest;
use App\Http\Requests\PostReplies\FetchPostRepliesRequest;
use App\Http\Requests\Posts\FetchPostsRequest;
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
     * @param $postReplyId
     * @param FetchPostRepliesRequest $postsRequest
     * @return JsonResponse
     */
    public function getComments($postReplyId,FetchPostRepliesRequest $postsRequest): JsonResponse
    {
        return $this->_commentService->getPostReplies($postReplyId,$postsRequest);
    }
}
