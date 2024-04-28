<?php

namespace App\Http\Controllers\DiscussifyCore;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\InitializeForumRequest;
use App\Services\DiscussifyCore\InitializeForumService;
use Illuminate\Http\JsonResponse;

class InitializeForumController extends Controller
{
    private InitializeForumService $_forumService;

    public function __construct(InitializeForumService $forumService)
    {
        $this->_forumService = $forumService;
    }

    /**
     * @param InitializeForumRequest $initializeForumRequest
     * @return JsonResponse
     */
    public function initializeForum(InitializeForumRequest $initializeForumRequest): JsonResponse
    {
        return $this->_forumService->initializeForum($initializeForumRequest);
    }

    /**
     * @return JsonResponse
     */
    public function getForumStats(): JsonResponse
    {
        return $this->_forumService->getForumStatistics();
    }
}
