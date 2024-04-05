<?php

namespace App\Utils\Helpers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class ModelCrudHelpers
{
    /**
     * @param ModelNotFoundException|\Exception $e
     * @return JsonResponse
     */
    public static function itemNotFoundError(ModelNotFoundException|\Exception $e): JsonResponse
    {
        $fullyQualifiedName = $e->getModel();
        $className = class_basename($fullyQualifiedName);

        return ResponseHelpers::ConvertToJsonResponseWrapper(
            ['error' => $className . ' not found'],
            $className . ' not found',
            404
        );
    }

    /**
     * @param $sourceUrl
     * @return void
     */
    public static function deleteImageFromStorage($sourceUrl): void
    {
        // Extract the file path from the URL
        $filePath = public_path(parse_url($sourceUrl, PHP_URL_PATH));

        // Check if the file exists before attempting to delete it
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }
}
