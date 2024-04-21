<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ManageFeedbackService;
use App\Services\Admin\ManageUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManageFeedbackController extends Controller
{
    /**
     * @var ManageFeedbackService
     */
    private ManageFeedbackService $manageFeedbackService;

    public function __construct(ManageFeedbackService $manageFeedbackService)
    {
        $this->manageFeedbackService = $manageFeedbackService;
    }

    /**
     * @return JsonResponse
     */
    public function getCustomerFeedbacks()
    {
        return $this->manageFeedbackService->getFeedback();
    }
}
