<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FetchUsersRequest;
use App\Services\Admin\ManageUserService;
use Illuminate\Http\JsonResponse;

class ManageUserController extends Controller
{

    /**
     * @var ManageUserService
     */
    private ManageUserService $_manageUserService;

    public function __construct(ManageUserService $manageUserService)
    {
        $this->_manageUserService = $manageUserService;
    }

    /**
     * @param FetchUsersRequest $usersRequest
     * @return JsonResponse
     */
    public function getUsers(FetchUsersRequest $usersRequest)
    {
        return $this->_manageUserService->getAllUsers($usersRequest);
    }

    /**
     * @param $userId
     * @return JsonResponse
     */
    public function getUserById($userId)
    {
        return $this->_manageUserService->getUserById($userId);
    }

    /**
     * @param $userId
     * @return JsonResponse
     */
    public function toggleUserStatus($userId)
    {
        return $this->_manageUserService->toggleUser($userId);
    }
}
