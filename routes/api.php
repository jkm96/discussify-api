<?php

use App\Http\Controllers\DiscussifyCore\InitializeForumController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/initialize', 'namespace' => 'api/v1', 'middleware' => 'api'], function () {
    Route::get('stats', [InitializeForumController::class, 'getForumStats']);
    Route::middleware(Authenticate::using('sanctum'))->group(function () {
        Route::post('', [InitializeForumController::class, 'initializeForum']);
    });
});

