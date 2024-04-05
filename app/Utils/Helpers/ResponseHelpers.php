<?php

namespace App\Utils\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ResponseHelpers
{
    /**
     * @param $data
     * @param $message
     * @param $statusCode
     * @return JsonResponse
     */
    public static function ConvertToJsonResponseWrapper($data, $message, $statusCode): JsonResponse
    {
        $succeeded = $statusCode === 200;

        $response = [
            "data" => $data,
            "statusCode" => $statusCode,
            "message" => $message,
            "succeeded" => $succeeded,
        ];

        return response()->json($response, $statusCode);
    }

    public static function ConvertToPagedJsonResponseWrapper($data, $message, $statusCode, $pagination = null): JsonResponse
    {
        $succeeded = $statusCode === 200;

        // Check if $pagination is an instance of LengthAwarePaginator
        $paginationData = null;
        if ($pagination instanceof LengthAwarePaginator) {
            $paginationData = [
                'current_page' => $pagination->currentPage(),  // Current page number
                'total_pages' => $pagination->lastPage(),   // Total available page number
                'page_size' => $pagination->perPage(),    // Number of items per page
                'total_count' => $pagination->total(),         // Total number of items across all pages
                'last_page' => $pagination->lastPage(),   // Last available page number
                'from' => $pagination->firstItem(),       // Starting index of items on the current page
                'to' => $pagination->lastItem(),           // Ending index of items on the current page
            ];
        }

        $response = [
            "data" => [
                'paging_metaData' => $paginationData,
                'data' => $data
            ],
            "statusCode" => $statusCode,
            "message" => $message,
            "succeeded" => $succeeded,
        ];

        return response()->json($response, $statusCode);
    }
}
