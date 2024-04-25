<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ManageFeedbackController;
use App\Http\Controllers\Admin\ManageUserController;
use App\Http\Controllers\DiscussifyCore\CategoryController;
use App\Http\Controllers\DiscussifyCore\ForumController;
use App\Http\Controllers\DiscussifyCore\PostController;
use App\Http\Controllers\DiscussifyCore\PostReplyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/posts', 'namespace' => 'api/v1', 'middleware' => 'api'], function () {
    Route::get('', [PostController::class, 'getPosts']);
    Route::get('cover-posts', [PostController::class, 'getCoverPosts']);
    Route::get('{slug}', [PostController::class, 'getPostBySlug']);
    Route::get('{slug}/replies', [PostReplyController::class, 'getPostReplies']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('create', [PostController::class, 'createPost']);
        Route::put('{post_id}/update', [PostController::class, 'updatePost']);
    });
});
