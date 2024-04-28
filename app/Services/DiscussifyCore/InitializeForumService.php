<?php

namespace App\Services\DiscussifyCore;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ForumResource;
use App\Http\Resources\ForumStatisticsResource;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Forum;
use App\Models\ForumStatistics;
use App\Models\User;
use App\Utils\Helpers\AuthHelpers;
use App\Utils\Helpers\ModelCrudHelpers;
use App\Utils\Helpers\ResponseHelpers;
use App\Utils\Traits\RecordFilterTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class InitializeForumService
{
    use RecordFilterTrait;

    /**
     * @param $createForumRequest
     * @return JsonResponse
     */
    public function initializeForum($createForumRequest): JsonResponse
    {
        try {
            ForumStatistics::truncate();

            $forum = ForumStatistics::create([
                'forum_id' => trim($createForumRequest['forum_id']),
                'forum_name' => trim($createForumRequest['forum_name']),
                'forum_description' => trim($createForumRequest['forum_description']),
                'posts' => 0,
                'members' => 0,
            ]);

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new ForumResource($forum),
                'initialized forum created successfully',
                200
            );
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error creating forum ',
                500
            );
        }
    }

    /**
     * @return JsonResponse
     */
    public function getForumStatistics(): JsonResponse
    {
        try {
            $forumStat = ForumStatistics::first();

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new ForumStatisticsResource($forumStat),
                'Forums retrieved successfully',
                200
            );
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error retrieving forum stats',
                500
            );
        }
    }

}
