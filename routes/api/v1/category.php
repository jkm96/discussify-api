<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ManageFeedbackController;
use App\Http\Controllers\Admin\ManageUserController;
use App\Http\Controllers\DiscussifyCore\CategoryController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/categories', 'namespace' => 'api/v1', 'middleware' => 'api'], function () {
    Route::get('', [CategoryController::class, 'getCategories']);
    Route::get('{slug}', [CategoryController::class, 'getCategoryBySlug']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'v1/manage-categories', 'middleware' => 'api'], function () {
        Route::post('create', [CategoryController::class, 'createCategory']);
    });
});
