<?php

namespace App\Console\Commands;

use App\Models\ForumStatistics;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
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
        if ($forumStatistics){
            Log::info($forumStatistics);
            $totalMembers = User::count();
            $totalPosts = Post::count();

            $forumStatistics->members = $totalMembers;
            $forumStatistics->posts = $totalPosts;
            $forumStatistics->save();

            $users = User::with('posts', 'postReplies', 'comments')->get();

            foreach ($users as $user) {
                $postCount = $user->posts()->count();
                $replyCount = $user->postReplies()->count();
                $commentsCount = $user->comments()->count();

                $user->posts_count = $postCount;
                $user->post_replies_count = $replyCount;
                $user->comments_count = $commentsCount;
                $user->save();

                Log::info("User: {$user->name}, Posts: {$postCount}, Replies: {$replyCount}");
            }

            $posts = Post::with( 'postReplies', 'comments')->get();

            foreach ($posts as $post) {
                $replyCount = $post->postReplies()->count();
                $commentsCount = $post->comments()->count();
                $participants = $post->postReplies->pluck('user_id')->unique()->count();

                $post->post_replies_count = $replyCount;
                $post->participants = $participants;
                $post->comments_count = $commentsCount;
                $post->save();

                Log::info("Post: {$post->title}, Participants: {$participants},Comments: {$commentsCount}, Replies: {$replyCount}");
            }

            Log::info('Forum statistics updated successfully.');
        }
    }
}
