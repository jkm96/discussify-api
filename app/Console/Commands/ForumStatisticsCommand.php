<?php

namespace App\Console\Commands;

use App\Models\Forum;
use App\Models\ForumStatistics;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ForumStatisticsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:forum-statistics-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to calculate forum statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $forumStatistics = ForumStatistics::first();
        if ($forumStatistics) {
            $totalMembers = User::count();
            $totalPosts = Post::count();

            $forumStatistics->members = $totalMembers;
            $forumStatistics->posts = $totalPosts;
            $forumStatistics->save();

            $this->calculateUserStats();

            $this->calculatePostStats();

            $this->calculateForumStats();

            Log::info('Forum statistics updated successfully.');
        }
    }

    /**
     * @return void
     */
    public function calculateUserStats(): void
    {
        $perPage = 10;
        $page = 1;

        $users = $this->getUsers($perPage, $page);

        while ($page <= $users->lastPage()) {

            foreach ($users as $user) {
                $postCount = $user->posts()->count();
                $replyCount = $user->postReplies()->count();
                $commentsCount = $user->comments()->count();
                $followingCount = $user->following()->count();
                $followers = $user->followers()->count();

                $likesCount = $user->posts()->with('postLikes')->get()->map(function ($post) {
                    return $post->postLikes->count();
                })->sum();

                $user->posts_count = $postCount;
                $user->post_replies_count = $replyCount;
                $user->comments_count = $commentsCount;
                $user->following = $followingCount;
                $user->save();

                Log::info("User: {$user->name}, Followers: {$followers},Following: {$followingCount}, Posts: {$postCount}, Replies: {$replyCount}");
            }

            // Move to the next page
            $page++;

            // Fetch the next page of users
            $users = $this->getUsers($perPage, $page);
        }
    }

    /**
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function getUsers(int $perPage, int $page): LengthAwarePaginator
    {
        return User::with('posts', 'postReplies', 'comments')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @return void
     */
    public function calculatePostStats(): void
    {
        $perPage = 10;
        $page = 1;

        $posts = $this->getPosts($perPage, $page);

        while ($page <= $posts->lastPage()) {
            foreach ($posts as $post) {
                $replyCount = $post->postReplies()->count();
                $commentsCount = $post->comments()->count();
                $participants = $post->postReplies->pluck('user_id')->unique()->count();

                $post->post_replies_count = $replyCount;
                $post->participants = $participants;
                $post->comments_count = $commentsCount;
                $post->likes = $post->postLikes()->count();
                $post->save();

                Log::info("Post: {$post->title}, Participants: {$participants},Comments: {$commentsCount}, Replies: {$replyCount}");
            }

            // Move to the next page
            $page++;

            // Fetch the next page of posts
            $posts = $this->getPosts($perPage, $page);
        }
    }

/**
     * @return void
     */
    public function calculateForumStats(): void
    {
        $perPage = 10;
        $page = 1;

        $forums = $this->getForums($perPage, $page);

        while ($page <= $forums->lastPage()) {
            foreach ($forums as $forum) {
                $postCount = $forum->posts()->count();

                $forum->post_count = $postCount;
                $forum->save();

                Log::info("Forum: {$forum->title}, Posts: {$postCount}");
            }

            // Move to the next page
            $page++;

            // Fetch the next page of posts
            $forums = $this->getPosts($perPage, $page);
        }
    }

    /**
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function getPosts(int $perPage, int $page): LengthAwarePaginator
    {
        return Post::with('postReplies', 'comments')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function getForums(int $perPage, int $page): LengthAwarePaginator
    {
        return Forum::with('posts')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
