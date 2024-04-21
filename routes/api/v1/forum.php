<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ManageFeedbackController;
use App\Http\Controllers\Admin\ManageUserController;
use App\Http\Controllers\DiscussifyCore\CategoryController;
use App\Http\Controllers\DiscussifyCore\ForumController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/forums', 'namespace' => 'api/v1', 'middleware' => 'api'], function () {
    Route::get('', [ForumController::class, 'getForums']);
    Route::get('{slug}', [ForumController::class, 'getForumBySlug']);
    Route::get('{slug}/posts', [ForumController::class, 'getForumPosts']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'v1/manage-forums', 'namespace' => 'api/v1', 'middleware' => 'api'], function () {
        Route::post('create', [ForumController::class, 'createForum']);
    });
});
