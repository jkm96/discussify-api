<?php

namespace App\Http\Controllers\DiscussifyCore;

use App\Http\Controllers\Controller;
use App\Http\Requests\Posts\CreatePostRequest;
use App\Http\Requests\Posts\FetchPostsFormRequest;
use App\Http\Requests\Posts\UpdatePostRequest;
use App\Services\DiscussifyCore\PostService;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    private PostService $_postService;

    public function __construct(PostService $PostService)
    {
        $this->_postService = $PostService;
    }

    /**
     * @param CreatePostRequest $createPostRequest
     * @return JsonResponse
     */
    public function createPost(CreatePostRequest $createPostRequest): JsonResponse
    {
        return $this->_postService->addNewPost($createPostRequest);
    }

    /**
     * @param $postId
     * @param UpdatePostRequest $updatePostRequest
     * @return JsonResponse
     */
    public function updatePost($postId,UpdatePostRequest $updatePostRequest): JsonResponse
    {
        return $this->_postService->editPost($postId,$updatePostRequest);
    }

    /**
     * @param FetchPostsFormRequest $postsRequest
     * @return JsonResponse
     */
    public function getPosts(FetchPostsFormRequest $postsRequest): JsonResponse
    {
        return $this->_postService->getPosts($postsRequest);
    }

    /**
     * @return JsonResponse
     */
    public function getCoverPosts(): JsonResponse
    {
        return $this->_postService->getCoverPosts();
    }

    /**
     * @param $slug
     * @return JsonResponse
     */
    public function getPostBySlug($slug): JsonResponse
    {
        return $this->_postService->getPostBySlug($slug);
    }
}
