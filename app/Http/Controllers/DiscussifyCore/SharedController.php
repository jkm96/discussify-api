<?php

namespace App\Http\Controllers\DiscussifyCore;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\Shared\LikeRequest;
use App\Services\DiscussifyCore\SharedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SharedController extends Controller
{
    /**
     * @var SharedService
     */
    private SharedService $_sharedService;

    public function __construct(SharedService $sharedService)
    {
        $this->_sharedService = $sharedService;
    }

    /**
     * @param LikeRequest $likeRequest
     * @return JsonResponse
     */
    public function toggleRecordLike(LikeRequest $likeRequest)
    {
        return $this->_sharedService->toggleLike($likeRequest);
    }
}
