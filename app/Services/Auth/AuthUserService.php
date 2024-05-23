<?php

namespace App\Services\Auth;

use App\Jobs\DispatchEmailNotificationsJob;
use App\Models\User;
use App\Models\UserVerification;
use App\Utils\Enums\EmailTypes;
use App\Utils\Helpers\AuthHelpers;
use App\Utils\Helpers\ResponseHelpers;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthUserService
{
    /**
     * @param $request
     * @return JsonResponse
     */
    public function registerUser($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $userAvatarUrl = AuthHelpers::createUserAvatarFromName(trim($request['username']), true);
            $profileCoverUrl = AuthHelpers::createUserAvatarFromName(trim($request['username']), false);

            $user = User::create([
                'username' => $request['username'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'profile_url' => $userAvatarUrl,
                'profile_cover_url' => $profileCoverUrl,
                'is_active' => 1
            ]);

            //send email verification message
            $token = Str::random(70);
            $verificationUrl = env('DISCUSSIFY_FRONTEND_URL') . '/auth/verify-user?token=' . $token;
            UserVerification::create([
                'user_id' => $user->id,
                'token' => $token
            ]);

            DB::commit();

            $details = [
                'type' => EmailTypes::USER_VERIFICATION->name,
                'recipientEmail' => trim($request['email']),
                'username' => trim($request['username']),
                'verificationUrl' => trim($verificationUrl),
            ];

            DispatchEmailNotificationsJob::dispatch($details);

            $tokenResource = AuthHelpers::getUserTokenResource($user, 0);

            return ResponseHelpers::ConvertToJsonResponseWrapper($tokenResource, 'Registered successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseHelpers::ConvertToJsonResponseWrapper(['error' => $e->getMessage()], 'Error during registration', 500);
        }
    }


    /**
     * @param $verificationRequest
     * @return JsonResponse
     */
    public function verifyUserEmail($verificationRequest): JsonResponse
    {
        $userVerify = UserVerification::where('token', $verificationRequest->token)->first();
        $message = 'Verification token has expired.';

        if (!is_null($userVerify)) {
            $user = $userVerify->user;
            Log::info(json_encode($user));

            $expirationDate = Carbon::parse($userVerify->created_at)->addDays(7);
            if (Carbon::now()->gt($expirationDate)) {
                $userVerify->delete();
                return ResponseHelpers::ConvertToJsonResponseWrapper([], $message, 400);
            }

            if (!$user->is_email_verified) {
                $userVerify->user->is_email_verified = 1;
                $userVerify->user->email_verified_at = Carbon::now();
                $userVerify->user->save();
                $message = "Email has been verified.";
            } else {
                $message = "Email has been verified.";
            }

            return ResponseHelpers::ConvertToJsonResponseWrapper([], $message, 200);
        }

        return ResponseHelpers::ConvertToJsonResponseWrapper([], $message, 400);
    }

    /**
     * @param $loginRequest
     * @return JsonResponse
     */
    public function loginUser($loginRequest): JsonResponse
    {
        try {
            $credentials = filter_var($loginRequest['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            if (Auth::attempt([$credentials => $loginRequest['username'], 'password' => $loginRequest['password'], 'is_active' => 1])) {
                $user = User::where('email', $loginRequest['username'])
                    ->orWhere('username', $loginRequest['username'])
                    ->firstOrFail();

                $tokenResource = AuthHelpers::getUserTokenResource($user, 0);

                return ResponseHelpers::ConvertToJsonResponseWrapper($tokenResource, "logged in successfully", 200);
            }

            return ResponseHelpers::ConvertToJsonResponseWrapper([], "Login information is invalid.", 401);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(['error' => $e->getMessage()], 'Error during user login', 500);
        }
    }

    /**
     * @return JsonResponse
     */
    public function logoutUser(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return ResponseHelpers::ConvertToJsonResponseWrapper([], "logged out successfully", 200);
    }

    /**
     * @param $forgotPasswordRequest
     * @return JsonResponse
     */
    public function forgotPassword($forgotPasswordRequest): JsonResponse
    {
        try {
            $email = trim($forgotPasswordRequest["email"]);
            $userExists = DB::table('users')->where('email', $email)->first();
            if ($userExists == null) {
                return ResponseHelpers::ConvertToJsonResponseWrapper(
                    [],
                    "A password reset link has been sent to " . $forgotPasswordRequest["email"],
                    200);
            }

            //generate token
            $token = Str::random(65);
            DB::table('password_reset_tokens')->insert([
                'email' => trim($email),
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
            //send email
            $resetPassUrl = env('PET_DIARIES_FRONTEND_URL') . '/auth/reset-password?email=' . $email . '&token=' . $token;
            $details = [
                'type' => EmailTypes::USER_FORGOT_PASSWORD->name,
                'recipientEmail' => $email,
                'username' => trim($userExists->username),
                'resetPassUrl' => trim($resetPassUrl),
            ];

            DispatchEmailNotificationsJob::dispatch($details);

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                [],
                "A password reset link has been sent to " . $forgotPasswordRequest["email"],
                200);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(['error' => $e->getMessage()], 'Error during password reset request', 400);
        }
    }

    /**
     * @param $resetPassRequest
     * @return JsonResponse
     */
    public function resetPassword($resetPassRequest): JsonResponse
    {
        try {
            $email = trim($resetPassRequest['email']);
            $password = trim($resetPassRequest['password']);
            $token = $resetPassRequest['token'];

            $checkToken = DB::table('password_reset_tokens')->where([
                'email' => $email,
                'token' => $token
            ])->first();

            if (!$checkToken) {
                return ResponseHelpers::ConvertToJsonResponseWrapper(
                    ['error' => 'invalid token'],
                    'Invalid token. Try requesting for another reset',
                    400
                );
            }

            User::where('email', $email)->update(['password' => Hash::make($password)]);
            DB::table('password_reset_tokens')->where(['email' => $email])->delete();

            return ResponseHelpers::ConvertToJsonResponseWrapper([], 'Password changed successfully.', 200);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(['error' => $e->getMessage()], 'Error during password reset', 400);
        }
    }
}
