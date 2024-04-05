<?php

namespace App\Services\Admin;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Utils\Helpers\ModelCrudHelpers;
use App\Utils\Helpers\ResponseHelpers;
use App\Utils\Traits\DateFilterTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ManageUserService
{
    use DateFilterTrait;

    /**
     * @param $userQueryParams
     * @return JsonResponse
     */
    public function getAllUsers($userQueryParams)
    {
        try {
            $pageSize = $userQueryParams['page_size'] ?? 10;
            $currentPage = $userQueryParams['page_number'] ?? 1;

            $query = User::orderBy('created_at', 'desc');
            $this->applyFilters($query, $userQueryParams);
            $users = $query->paginate($pageSize, ['*'], 'page', $currentPage);

            return ResponseHelpers::ConvertToPagedJsonResponseWrapper(
                UserResource::collection($users->items()),
                'Users retrieved successfully',
                200,
                $users
            );
        } catch (Exception $e) {
            Log::error('Exception when retrieving users: ' . $e->getMessage());

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error retrieving users',
                $e->getCode() ?: 500
            );
        }
    }

    /**
     * @param $query
     * @param $params
     * @return void
     */
    private function applyFilters($query, $params)
    {
        Log::info($params);
        $this->applyDateFilters($query, $params['period_from'] ?? null, $params['period_to'] ?? null);
        $this->applySearchTermFilter($query, $params['search_term'] ?? null);
        $this->applySubscriptionFilter($query, $params['is_subscribed'] ?? null);
    }

    /**
     * @param $query
     * @param $searchTerm
     * @return void
     */
    private function applySearchTermFilter($query, $searchTerm)
    {
        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('username', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }
    }

    /**
     * @param $query
     * @param $subscribed
     * @return void
     */
    private function applySubscriptionFilter($query, $subscribed)
    {
        if ($subscribed !== null) {
            $query->where('is_subscribed', $subscribed);
        }
    }

    /**
     * @param $userId
     * @return JsonResponse
     */
    public function getUserById($userId)
    {
        try {
            $user = User::findOrFail($userId);

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new UserResource($user),
                'User fetched successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error fetching user details',
                500
            );
        }
    }

    /**
     * @param $userId
     * @return JsonResponse
     */
    public function toggleUser($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->is_active = $user->is_active ? 0 : 1;
            $user->update();

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['user' => $user],
                'User toggled successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error toggling user',
                500
            );
        }
    }
}
