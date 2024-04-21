<?php

namespace App\Http\Controllers\DiscussifyCore;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\CreateForumRequest;
use App\Http\Requests\Posts\FetchPostsRequest;
use App\Services\DiscussifyCore\CategoryService;
use App\Services\DiscussifyCore\ForumService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * @return JsonResponse
     */
    public function getForums(): JsonResponse
    {
        return $this->_forumService->getForums();
    }

    /**
     * @return JsonResponse
     */
    public function getForumPosts($slug, FetchPostsRequest $fetchPostsRequest): JsonResponse
    {
        return $this->_forumService->getForumPosts($slug,$fetchPostsRequest);
    }

    /**
     * @return JsonResponse
     */
    public function getForumStats(): JsonResponse
    {
        return $this->_forumService->getForumStatistics();
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
