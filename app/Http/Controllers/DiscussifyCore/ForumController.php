<?php

namespace App\Http\Controllers\DiscussifyCore;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\CreateForumRequest;
use App\Http\Requests\Forum\EditForumRequest;
use App\Http\Requests\Posts\FetchPostsFormRequest;
use App\Services\DiscussifyCore\ForumService;
use Illuminate\Http\JsonResponse;

class ForumController extends Controller
{

    private ForumService $_forumService;

    public function __construct(ForumService $forumService)
    {
        $this->_forumService = $forumService;
    }

    /**
     * @param CreateForumRequest $createForumRequest
     * @return JsonResponse
     */
    public function createForum(CreateForumRequest $createForumRequest): JsonResponse
    {
        return $this->_forumService->addNewForum($createForumRequest);
    }

    /**
     * @param $forumId
     * @param EditForumRequest $editForumRequest
     * @return JsonResponse
     */
    public function editForum($forumId,EditForumRequest $editForumRequest): JsonResponse
    {
        return $this->_forumService->updateForum($forumId,$editForumRequest);
    }

    /**
     * @return JsonResponse
     */
    public function getForums(): JsonResponse
    {
        return $this->_forumService->getForums();
    }

    /**
     * @param $slug
     * @param FetchPostsFormRequest $fetchPostsRequest
     * @return JsonResponse
     */
    public function getForumPosts($slug, FetchPostsFormRequest $fetchPostsRequest): JsonResponse
    {
        return $this->_forumService->getForumPosts($slug,$fetchPostsRequest);
    }

    /**
     * @param $slug
     * @return JsonResponse
     */
    public function getForumBySlug($slug): JsonResponse
    {
        return $this->_forumService->getForumBySlug($slug);
    }
}
