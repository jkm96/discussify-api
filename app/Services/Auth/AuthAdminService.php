<?php

namespace App\Services\Auth;

use App\Http\Resources\AdminResource;
use App\Models\Admin;
use App\Utils\Helpers\AuthHelpers;
use App\Utils\Helpers\ResponseHelpers;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthAdminService
{
    /**
     * @param $request
     * @return JsonResponse
     */
    public function registerAdmin($request): JsonResponse
    {
        try {
            $user = Admin::create([
                'username' => $request['username'],
                'email' => $request['email'],
                'is_active' => 0,
                'password' => Hash::make($request['password']),
            ]);

            return ResponseHelpers::ConvertToJsonResponseWrapper(new AdminResource($user), 'Registered successfully', 200);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(['error' => $e->getMessage()], 'Error during admin registration', 500);
        }
    }

    /**
     * @param $loginRequest
     * @return JsonResponse
     */
    public function loginAdmin($loginRequest): JsonResponse
    {
        try {
            $emailOrUsername = $loginRequest["username"];
            $admin = Admin::where('email', $emailOrUsername)
                ->orWhere('username', $emailOrUsername)
                ->first();

            if ($admin == null)
                return ResponseHelpers::ConvertToJsonResponseWrapper([], 'Login information is invalid.', 400);

            if (Hash::check($loginRequest["password"], $admin->password) && $admin->is_active == 1) {
                $tokenResource = AuthHelpers::getUserTokenResource($admin, 1);

                return ResponseHelpers::ConvertToJsonResponseWrapper($tokenResource, 'logged in successfully', 200);
            }

            return ResponseHelpers::ConvertToJsonResponseWrapper([], 'Login information is invalid.', 400);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(['error' => $e->getMessage()], 'Error during admin login', 500);
        }
    }

    /**
     * @return JsonResponse
     */
    public function logoutAdmin(): JsonResponse
    {
        auth()->user()->tokens()->delete();
        return ResponseHelpers::ConvertToJsonResponseWrapper([], "logged out successfully", 200);
    }
}
