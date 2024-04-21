<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Requests\Auth\UserRegistrationRequest;
use App\Http\Requests\Auth\UserVerificationRequest;
use App\Services\Auth\AuthUserService;
use Illuminate\Http\JsonResponse;

class AuthUserController extends Controller
{
    /**
     * @var AuthUserService
     */
    private $_userService;

    public function __construct(AuthUserService $userService)
    {
        $this->_userService = $userService;
    }

    /**
     * Register a new user
     *
     * This endpoint lets you add a new user into the storage
     * @param UserRegistrationRequest $request
     * @return JsonResponse
     */
    public function registerUser(UserRegistrationRequest $request): JsonResponse
    {
        return $this->_userService->registerUser($request);
    }

    /**
     * Verify a new user email address
     *
     * This endpoint lets you verify user's email
     * @param UserVerificationRequest $request
     * @return JsonResponse
     */
    public function verifyUserEmail(UserVerificationRequest $request)
    {
        return $this->_userService->verifyUserEmail($request);
    }

    /**
     * Login user into the system
     *
     * This endpoint lets you sign in a user into the system
     * Throws an error if the login credentials are incorrect
     * @param UserLoginRequest $loginRequest
     * @return JsonResponse
     */
    public function loginUser(UserLoginRequest $loginRequest)
    {
        return $this->_userService->loginUser($loginRequest->validated());
    }

    /**
     * Request for a password reset
     * @param ForgotPasswordRequest $forgotPasswordRequest
     * @return JsonResponse
     */
    public function requestPasswordReset(ForgotPasswordRequest $forgotPasswordRequest)
    {
        return $this->_userService->forgotPassword($forgotPasswordRequest);
    }

    /**
     * Request for a password reset
     * @param ResetPasswordRequest $resetPasswordRequest
     * @return JsonResponse
     */
    public function changePassword(ResetPasswordRequest $resetPasswordRequest)
    {
        return $this->_userService->resetPassword($resetPasswordRequest);
    }

    /**
     * Logout a user
     *
     * This endpoint lets you sign out a user from the system
     * @return JsonResponse
     */
    public function logoutUser()
    {
        return $this->_userService->logoutUser();
    }
}
