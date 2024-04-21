<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Http\Requests\Auth\AdminRegisterRequest;
use App\Services\Auth\AuthAdminService;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    /**
     * @var AuthAdminService
     */
    private $_adminService;

    public function __construct(AuthAdminService $adminService)
    {
        $this->_adminService = $adminService;
    }

    /**
     * Register a new admin
     *
     * This endpoint lets you add a new admin into the storage
     * @param AdminRegisterRequest $request
     * @return JsonResponse
     */
    public function registerAdmin(AdminRegisterRequest $request)
    {
        return $this->_adminService->registerAdmin($request->validated());
    }

    /**
     * Login admin into the system
     *
     * This endpoint lets you sign in an admin into the system
     * Throws an error if the login credentials are incorrect
     * @param AdminLoginRequest $loginRequest
     * @return JsonResponse
     */
    public function loginAdmin(AdminLoginRequest $loginRequest)
    {
        return $this->_adminService->loginAdmin($loginRequest->validated());
    }

    /**
     * Logout an admin
     *
     * This endpoint lets you sign out an admin from the system
     * @return JsonResponse
     */
    public function logoutAdmin()
    {
        return $this->_adminService->logoutAdmin();
    }
}
