<?php

namespace App\Services\Content;

use App\Http\Resources\SiteContentResource;
use App\Models\CustomerFeedback;
use App\Models\SiteContent;
use App\Utils\Helpers\ModelCrudHelpers;
use App\Utils\Helpers\ResponseHelpers;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class SiteContentService
{

    /**
     * @param $contentRequest
     * @return JsonResponse
     */
    public function fetchSiteContentByType($contentRequest): JsonResponse
    {
        $type = trim($contentRequest['type']);
        try {
            $query = SiteContent::query();

            if ($type !== 'all') {
                $query->where('type', $type);
            }

            $content = $query->orderBy('created_at', 'desc')->get();

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new SiteContentResource($content),
                'Site content fetched successfully',
                200
            );
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error fetching site content',
                500
            );
        }
    }

    /**
     * @param $contentId
     * @return JsonResponse
     */
    public function fetchSiteContentById($contentId): JsonResponse
    {
        try {
            $content = SiteContent::findOrFail($contentId);

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new SiteContentResource($content),
                'Site content fetched successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error fetching site content',
                500
            );
        }
    }

    /**
     * @param $siteContentRequest
     * @return JsonResponse
     */
    public function addSiteContent($siteContentRequest): JsonResponse
    {
        try {
            $content = SiteContent::create([
                'title' => $siteContentRequest['title'],
                'content' => $siteContentRequest['content'],
                'type' => trim($siteContentRequest['type'])//privacy, terms
            ]);

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new SiteContentResource($content),
                'Site content created successfully',
                200
            );
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error creating site content',
                500
            );
        }
    }

    /**
     * @param $feedbackRequest
     * @return JsonResponse
     */
    public function addCustomerFeedback($feedbackRequest): JsonResponse
    {
        try {
            $content = CustomerFeedback::create([
                'email' => $feedbackRequest['email'],
                'rating' => $feedbackRequest['rating'],
                'feedback' => trim($feedbackRequest['feedback'])
            ]);

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new SiteContentResource($content),
                'Review submitted successfully',
                200
            );
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error submitting review',
                500
            );
        }
    }

    /**
     * @param $siteContentRequest
     * @param $contentId
     * @return JsonResponse
     */
    public function editSiteContent($siteContentRequest, $contentId): JsonResponse
    {
        try {
            $content = SiteContent::findOrFail($contentId);
            $content->title = $siteContentRequest['title'];
            $content->type = $siteContentRequest['type'];
            $content->content = $siteContentRequest['content'];

            $content->save();

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new SiteContentResource($content),
                'Site content updated successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error updating site content',
                500
            );
        }
    }
}
