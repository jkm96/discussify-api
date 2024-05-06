<?php

namespace App\Services\DiscussifyCore;

use App\Http\Resources\LikeResource;
use App\Http\Resources\PostResource;
use App\Models\Like;
use App\Models\User;
use App\Utils\Helpers\ModelCrudHelpers;
use App\Utils\Helpers\ResponseHelpers;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SharedService
{
    /**
     * @param $likeRequest
     * @return JsonResponse
     */
    public function toggleLike($likeRequest): JsonResponse
    {
        try {
            $type = trim($likeRequest['type']);
            $recordId = trim($likeRequest['record_id']);
            $user = User::findOrFail(Auth::id());

            $existingLike = Like::where('user_id', $user->id)
                ->where('likeable_type', "App\\Models\\" . ucfirst($type))
                ->where('likeable_id', $recordId)
                ->first();

            if ($existingLike) {
                // If the user has already liked the item, unlike it
                $existingLike->delete();
                $message = ucfirst($type) . ' unliked successfully';

                return ResponseHelpers::ConvertToJsonResponseWrapper(
                    new LikeResource($existingLike),
                    $message,
                    200
                );
            }

            // If the user has not liked the item, like it
            $like = new Like();
            $like->user_id = $user->id;
            $like->likeable_id = $recordId;
            $like->likeable_type = "App\\Models\\" . ucfirst($type);
            $like->save();
            $message = ucfirst($type) . ' liked successfully';

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new LikeResource($like),
                $message,
                200
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error retrieving post',
                500
            );
        }
    }
}
