<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Ensure users are seeded first
        $this->call(UsersTableSeeder::class);

        // Create 10 posts with comments and likes for each post
        Post::factory(10)->create()->each(function ($post) {
            // Create 5 comments for each post
            $post->comments()->saveMany(Comment::factory(5)->make());

            // Likes for posts
            $post->likes()->saveMany(Like::factory(3)->make());
        });

        // Create likes for comments
        Comment::all()->each(function ($comment) {
            $comment->likes()->saveMany(Like::factory(2)->make());
        });
    }
}

