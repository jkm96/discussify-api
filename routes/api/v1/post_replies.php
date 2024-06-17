<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ManageFeedbackController;
use App\Http\Controllers\Admin\ManageUserController;
use App\Http\Controllers\DiscussifyCore\CommentController;
use App\Http\Controllers\DiscussifyCore\PostReplyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/post-replies', 'namespace' => 'api/v1', 'middleware' => 'api'], function () {
    Route::get('{post_id}', [PostReplyController::class, 'getPostReplies']);
    Route::get('{post_reply_id}/comments', [CommentController::class, 'getComments']);
    Route::get('comments/{commentId}/nested-replies', [CommentController::class, 'getNestedReplies']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('create', [PostReplyController::class, 'createPostReply']);
        Route::put('{post_reply_id}/edit', [PostReplyController::class, 'updatePostReply']);
        Route::post('comments/create', [CommentController::class, 'createComment']);
        Route::post('comments/upsert', [CommentController::class, 'upsertReply']);
        Route::put('comments/{commentId}/edit', [CommentController::class, 'updateComment']);
    });
});
