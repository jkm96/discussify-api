<?php

namespace App\Services\DiscussifyCore;

use App\Http\Resources\LikeResource;
use App\Http\Resources\PostResource;
use App\Models\Follow;
use App\Models\Like;
use App\Models\User;
use App\Utils\Helpers\ModelCrudHelpers;
use App\Utils\Helpers\ResponseHelpers;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SharedService
{
    /**
     * @param $toggleFollowLikeRequest
     * @return JsonResponse
     */
    public function toggleFollowLike($toggleFollowLikeRequest): JsonResponse
    {
        try {
            $type = trim($toggleFollowLikeRequest['type']);
            $recordId = trim($toggleFollowLikeRequest['record_id']);

            $user = Auth::guard('api')->user();
            if (!$user) {
                return ResponseHelpers::ConvertToJsonResponseWrapper(
                    ['error' => 'Not authorized to modify resource'],
                    'Not authorized to modify resource',
                    401
                );
            }

            switch ($type) {
                case 'comment':
                case 'post':
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
                case 'author':
                    //handle following or unfollowing a user
                    $author = User::findOrFail($recordId);

                    // Check if the user is already followed by the current user
                    $isFollowed = Follow::where('user_id', $user->id)
                        ->where('followable_type', "App\\Models\\" . ucfirst("user"))
                        ->where('followable_id', $recordId)
                        ->first();

                    if ($isFollowed != null) {
                        // If the user is already followed, unfollow them
                        $user-> following()->detach($author->id);
                        $message = 'User unfollowed successfully';
                        $author->followers -= 1;
                    } else {
                        // If the user is not followed, follow them
                        $user->following()->attach($author->id);
                        $message = 'User followed successfully';
                        $author->followers += 1;
                    }

                    $author->save();

                    return ResponseHelpers::ConvertToJsonResponseWrapper(
                        ["message"=>$message],
                        $message,
                        200
                    );
            }

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => "Modifying item"],
                'Error Modifying item',
                500
            );

        } catch
        (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                // Duplicate entry error handling
                return ResponseHelpers::ConvertToJsonResponseWrapper(
                    ['error' => 'Duplicate entry for following'],
                    'You have already followed this user',
                    400
                );
            } else {
                // Other query exceptions
                return ResponseHelpers::ConvertToJsonResponseWrapper(
                    ['error' => $e->getMessage()],
                    'Error modifying item',
                    500
                );
            }
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error Modifying item',
                500
            );
        }
    }
}
