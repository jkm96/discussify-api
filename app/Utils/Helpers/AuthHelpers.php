<?php

namespace App\Utils\Helpers;

use App\Http\Resources\AdminResource;
use App\Http\Resources\TokenResource;
use App\Http\Resources\UserResource;
use App\Utils\Constants\AppConstants;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;

class AuthHelpers
{
    /**
     * @param $apiUser
     * @param $isAdmin
     * @return array
     */
    public static function getUserTokenResource($apiUser, $isAdmin): array
    {
        $token = $apiUser->createToken('auth-token-' . $apiUser->username, ['*'], Carbon::now()->addHours(12))->plainTextToken;
        $tokenDetails = $apiUser->tokens()->latest()->first();
        $tokenDetails->token = $token;

        if ($isAdmin) {
            $apiUser->auth_token = $token;
            $apiUser->update();

            return [
                "token" => new TokenResource($tokenDetails),
                "user" => new AdminResource($apiUser)
            ];
        }

        $graceDays = (int)env('DISCUSSIFY_EVGP');//email_verification_grace_period
        $creationDate = \Illuminate\Support\Carbon::parse($apiUser->created_at);
        $expirationDate = $creationDate->copy()->addDays($graceDays);
        $daysLeft = round(Carbon::now()->diffInDays($expirationDate, false));
        $gracePeriodExpired = Carbon::now()->greaterThan($expirationDate);

        $apiUser->setAttribute('created_at', $creationDate);
        $apiUser->setAttribute('grace_period_count', $daysLeft);
        $apiUser->setAttribute('is_grace_period_expired', $gracePeriodExpired);
        $apiUser->setAttribute('grace_period_expiration', $expirationDate);

        return [
            "token" => new TokenResource($tokenDetails),
            "user" => new UserResource($apiUser)
        ];
    }

    /**
     * @param $username
     * @param $isProfilePicture
     * @return string
     * @throws Exception
     */
    public static function createUserAvatarFromName($username, $isProfilePicture): string
    {
        // Extract first and last letters as initials
        $name = trim($username);
        $initials = strtoupper(substr($name, 0, 1));
        $lastLetterIndex = strlen($name) - 1;
        if ($lastLetterIndex > 0) {
            $initials .= strtoupper(substr($name, $lastLetterIndex, 1)); // Last letter
        }
        $nameToWrite = $isProfilePicture ? $initials : $name;

        // Define a background color and text color for the avatar
        $colors = ['#005BC4','#0E8AAA','#17C964','#338EF7','#004493'];
        $randomIndex = array_rand($colors);
        $randomColor = $colors[$randomIndex];
        $bgColor = $randomColor; //. str_pad(substr(md5($name), 0, 6), 6, '0'); // Use a unique color based on the name
        $textColor = '#ffffff';

        // Determine image dimensions based on whether it's a profile picture or not
        $imageWidth = $isProfilePicture ? 200 : 970;
        $imageHeight = $isProfilePicture ? 200 : 260;

        // Create an image with the initials and colors
        $image = imagecreatetruecolor($imageWidth, $imageHeight);
        $bg = imagecolorallocate($image, hexdec(substr($bgColor, 1, 2)), hexdec(substr($bgColor, 3, 2)), hexdec(substr($bgColor, 5, 2)));
        $text = imagecolorallocate($image, hexdec(substr($textColor, 1, 2)), hexdec(substr($textColor, 3, 2)), hexdec(substr($textColor, 5, 2)));
        imagefill($image, 0, 0, $bg);
        $font = public_path('fonts/robotoregular.ttf');

        // Adjust text position based on whether it's a profile picture or not
        $textBoundingBox = imagettfbbox(75, 0, $font, $nameToWrite);
        $textWidth = $textBoundingBox[4] - $textBoundingBox[0];
        $textHeight = $textBoundingBox[1] - $textBoundingBox[7];
        $textX = $isProfilePicture ? (200 - $textWidth) / 2 : ($imageWidth - $textWidth) / 2;
        $textY = $isProfilePicture ? (200 + $textHeight) / 2 : ($imageHeight + $textHeight) / 2;

        imagettftext($image, 75, 0, $textX, $textY, $text, $font, $nameToWrite);

        // Save the image to a file
        $constructName = AppConstants::$appName . '-' . $username . '-' . Carbon::now() . '.png';
        $imageName = Str::lower(str_replace(' ', '-', $constructName));
        $directoryPath = $isProfilePicture ? 'images/user_profile_avatars/' : 'images/user_profile_covers/';
        $fullDirectoryPath = public_path($directoryPath);
        if (!file_exists($fullDirectoryPath)) {
            mkdir($fullDirectoryPath, 0777, true);
        }

        if (!file_exists($fullDirectoryPath)) {
            // If the directory still doesn't exist after attempting to create it, handle the error appropriately
            // For example, throw an exception or log an error message
            throw new Exception("Failed to create directory: $fullDirectoryPath");
        }

        $imagePath = $fullDirectoryPath . $imageName;
        imagepng($image, $imagePath);
        imagedestroy($image);

        return url($directoryPath . $imageName);
    }
}
