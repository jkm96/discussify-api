<?php

namespace App\Http\Controllers\DiscussifyCore;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostReplies\CreatePostReplyRequest;
use App\Http\Requests\PostReplies\FetchPostRepliesFormRequest;
use App\Http\Requests\PostReplies\UpdatePostReplyRequest;
use App\Http\Requests\Posts\CreatePostRequest;
use App\Http\Requests\Posts\FetchPostsFormRequest;
use App\Http\Requests\Posts\UpdatePostRequest;
use App\Services\DiscussifyCore\PostReplyService;
use App\Services\DiscussifyCore\PostService;
use Illuminate\Http\JsonResponse;

class PostReplyController extends Controller
{
    private PostReplyService $_postReplyService;

    public function __construct(PostReplyService $postReplyService)
    {
        $this->_postReplyService = $postReplyService;
    }

    /**
     * @param CreatePostReplyRequest $postReplyRequest
     * @return JsonResponse
     */
    public function createPostReply(CreatePostReplyRequest $postReplyRequest): JsonResponse
    {
        return $this->_postReplyService->addNewPostReply($postReplyRequest);
    }

    /**
     * @param $postReplyId
     * @param UpdatePostReplyRequest $updateRequest
     * @return JsonResponse
     */
    public function updatePostReply($postReplyId,UpdatePostReplyRequest $updateRequest): JsonResponse
    {
        return $this->_postReplyService->editPostReply($postReplyId,$updateRequest);
    }

    /**
     * @param $postSlug
     * @param FetchPostRepliesFormRequest $postsRequest
     * @return JsonResponse
     */
    public function getPostReplies($postSlug, FetchPostRepliesFormRequest $postsRequest): JsonResponse
    {
        return $this->_postReplyService->getPostReplies($postSlug,$postsRequest);
    }
}
