<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use App\Models\Forum;
use Illuminate\Support\Facades\DB;

class ForumCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the tables
        Forum::truncate();
        Category::truncate();

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            'General Discussion' => [
                'is_system' =>false,
                'description' => 'A space for casual conversations and topics that don\'t fit into other categories.',
                'forums' => [
                    ['title' => 'Chit-Chat', 'description' => 'General conversations and casual discussions.'],
                    ['title' => 'Daily Life', 'description' => 'Share daily experiences and stories.'],
                    ['title' => 'Random Thoughts', 'description' => 'Post any random ideas or thoughts.'],
                ],
            ],
            'Announcements and News' => [
                'is_system' =>true,
                'description' => 'Official announcements, updates, and news related to the forum or the subject of the community.',
                'forums' => [
                    ['title' => 'Forum Updates', 'description' => 'Announcements related to forum software updates and changes.'],
                    ['title' => 'Community News', 'description' => 'News and updates about the community.'],
                    ['title' => 'Events', 'description' => 'Announcements about upcoming events, both online and offline.'],
                ],
            ],
            'Introductions' => [
                'is_system' =>false,
                'description' => 'A place where new members can introduce themselves, share a bit about their interests, and get welcomed by the community.',
                'forums' => [
                    ['title' => 'New Members', 'description' => 'Introduce yourself if you\'re new to the community.'],
                    ['title' => 'Returning Members', 'description' => 'Welcome back discussions for members who are rejoining the community.'],
                ],
            ],
            'Support and Feedback' => [
                'is_system' =>false,
                'description' => 'Ask for help with issues related to the forum or its subject matter and provide feedback or suggestions for improvement.',
                'forums' => [
                    ['title' => 'Technical Support', 'description' => 'Help with technical issues related to the forum or its topic.'],
                    ['title' => 'Site Feedback', 'description' => 'Suggestions and feedback about the forum site.'],
                    ['title' => 'Feature Requests', 'description' => 'Ideas and requests for new features or improvements.'],
                ],
            ],
            'Frequently Asked Questions (FAQ)' => [
                'is_system' =>true,
                'description' => 'Common questions and answers to help new and existing members quickly find information.',
                'forums' => [
                    ['title' => 'General FAQs', 'description' => 'Common questions about the forum itself.'],
                    ['title' => 'Topic-Specific FAQs', 'description' => 'FAQs related to the specific subject of the forum.'],
                    ['title' => 'New Member FAQs', 'description' => 'Information for new members to help them get started.'],
                ],
            ],
            'Off-Topic' => [
                'is_system' =>false,
                'description' => 'Discussions that are not related to the main focus of the forum.',
                'forums' => [
                    ['title' => 'Entertainment', 'description' => 'Discussions about movies, TV shows, music, and games.'],
                    ['title' => 'Hobbies', 'description' => 'Share and discuss hobbies and personal interests.'],
                    ['title' => 'Current Events', 'description' => 'Talk about current events and news outside the main topic.'],
                ],
            ],
            'Events and Meetups' => [
                'is_system' =>false,
                'description' => 'Organize and discuss events, both online and offline, such as webinars, meetups, conferences, or other community gatherings.',
                'forums' => [
                    ['title' => 'Online Events', 'description' => 'Webinars, live streams, and virtual meetups.'],
                    ['title' => 'Local Meetups', 'description' => 'Organize and discuss local, in-person meetups.'],
                    ['title' => 'Event Planning', 'description' => 'Collaborate on planning future events.'],
                ],
            ],
            'Resources and Guides' => [
                'is_system' =>false,
                'description' => 'Share useful resources, tutorials, guides, and other educational materials that can help members.',
                'forums' => [
                    ['title' => 'Tutorials', 'description' => 'Step-by-step guides and tutorials.'],
                    ['title' => 'Best Practices', 'description' => 'Tips and best practices related to the forum topic.'],
                    ['title' => 'Resource Library', 'description' => 'A collection of useful links and resources.'],
                ],
            ],
            'Marketplace or Classifieds' => [
                'is_system' =>true,
                'description' => 'Buy, sell, or trade items, services, or opportunities relevant to the community\'s interests.',
                'forums' => [
                    ['title' => 'Buy and Sell', 'description' => 'Post items or services for sale.'],
                    ['title' => 'Trade', 'description' => 'Offer and find items to trade.'],
                    ['title' => 'Service Requests', 'description' => 'Request or offer services.'],
                ],
            ],
            'Jobs and Careers' => [
                'is_system' =>true,
                'description' => 'Job postings, career advice, and professional development discussions.',
                'forums' => [
                    ['title' => 'Job Listings', 'description' => 'Post and find job opportunities.'],
                    ['title' => 'Career Advice', 'description' => 'Share and seek advice on careers and professional growth.'],
                    ['title' => 'Freelance Opportunities', 'description' => 'Freelance and contract job postings.'],
                ],
            ],
            'Rules and Guidelines' => [
                'is_system' =>true,
                'description' => 'The community\'s rules, guidelines, and code of conduct.',
                'forums' => [
                    ['title' => 'Community Rules', 'description' => 'The official rules of the community.'],
                    ['title' => 'Posting Guidelines', 'description' => 'Tips and guidelines for posting.'],
                    ['title' => 'Disciplinary Actions', 'description' => 'Information about bans, warnings, and other actions.'],
                ],
            ],
        ];

        foreach ($categories as $categoryName => $categoryData) {
            $category = Category::create([
                'name' => $categoryName,
                'description' => $categoryData['description'],
                'is_system' => $categoryData['is_system'],
            ]);

            foreach ($categoryData['forums'] as $forumData) {
                Forum::create([
                    'category_id' => $category->id,
                    'title' => $forumData['title'],
                    'description' => $forumData['description'],
                    'is_system' => $category->is_system,
                ]);
            }
        }
    }
}
