<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ManageFeedbackController;
use App\Http\Controllers\Admin\ManageUserController;
use App\Http\Controllers\DiscussifyCore\CategoryController;
use App\Http\Controllers\DiscussifyCore\ForumController;
use App\Http\Controllers\DiscussifyCore\PostController;
use App\Http\Controllers\DiscussifyCore\PostReplyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/post-replies', 'namespace' => 'api/v1', 'middleware' => 'api'], function () {
    Route::get('{post_id}', [PostReplyController::class, 'getPostReplies']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('create', [PostReplyController::class, 'createPostReply']);
        Route::put('{post_reply_id}/update', [PostReplyController::class, 'updatePostReply']);
    });
});
