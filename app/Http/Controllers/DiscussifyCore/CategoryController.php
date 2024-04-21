<?php

namespace App\Http\Controllers\DiscussifyCore;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Services\DiscussifyCore\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @var CategoryService
     */
    private CategoryService $_categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->_categoryService = $categoryService;
    }

    /**
     * @param CreateCategoryRequest $createCategoryRequest
     * @return JsonResponse
     */
    public function createCategory(CreateCategoryRequest $createCategoryRequest)
    {
        return $this->_categoryService->createCategory($createCategoryRequest);
    }

    /**
     * @return JsonResponse
     */
    public function getCategories()
    {
        return $this->_categoryService->getCategories();
    }

    /**
     * @return JsonResponse
     */
    public function getCategoryBySlug($slug)
    {
        return $this->_categoryService->getCategoryBySlug($slug);
    }
}
