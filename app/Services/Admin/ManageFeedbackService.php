<?php

namespace App\Services\Admin;

use App\Http\Resources\FeedbackResource;
use App\Http\Resources\UserResource;
use App\Models\CustomerFeedback;
use App\Utils\Helpers\ResponseHelpers;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ManageFeedbackService
{
    /**
     * @return JsonResponse
     */
    public function getFeedback()
    {
        try {
            $feedback = CustomerFeedback::get();
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                FeedbackResource::collection($feedback),
                'Customer feedback retrieved successfully',
                200,
            );
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error retrieving customer feedback',
                $e->getCode() ?: 500
            );
        }
    }
}
