<?php

namespace App\Services\DiscussifyCore;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\User;
use App\Utils\Helpers\ModelCrudHelpers;
use App\Utils\Helpers\ResponseHelpers;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class CategoryService
{
    public function createCategory($createCategoryRequest)
    {
        try {

            $category = Category::create([
                'name' => $createCategoryRequest['name']
            ]);

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new CategoryResource($category),
                'Category created successfully',
                200
            );
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error creating category ',
                500
            );
        }
    }

    /**
     * @return JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        try {
            $categories = Category::with(['forums.latestPost.user'])->get();

//            $categories->each(function ($category) {
//                $category->forums->each(function ($forum) {
//                    if ($forum->latestPost) {
//                        $forum->setAttribute('latest_post_title', $forum->latestPost->title);
//                        $forum->setAttribute('latest_post_created_at', $forum->latestPost->created_at);
//                        $forum->setAttribute('latest_post_author', $forum->latestPost->user->username);
//                        $forum->unsetRelation('latestPost');
//                    }
//                });
//            });

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                CategoryResource::collection($categories),
                'Categories retrieved successfully',
                200
            );
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error retrieving categories',
                500
            );
        }
    }

    /**
     * @param $slug
     * @return JsonResponse
     */
    public function getCategoryBySlug($slug): JsonResponse
    {
        try {
            $category = Category::with('forums')
                ->where('slug', $slug)
                ->firstOrFail();

            return ResponseHelpers::ConvertToJsonResponseWrapper(
                new CategoryResource($category),
                'Category retrieved successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return ModelCrudHelpers::itemNotFoundError($e);
        } catch (Exception $e) {
            return ResponseHelpers::ConvertToJsonResponseWrapper(
                ['error' => $e->getMessage()],
                'Error retrieving category',
                500
            );
        }
    }
}
