<?php

namespace Database\Seeders;

use App\Models\Video;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();

        $videos = [
            [
                'title' => 'Introduction to Laravel',
                'description' => 'Learn the basics of Laravel framework',
                'url' => 'https://example.com/videos/laravel-intro.mp4',
                'thumbnail' => 'https://example.com/thumbs/laravel.jpg',
                'duration' => 1200, // 20 minutes
                'is_premium' => false,
            ],
            [
                'title' => 'Advanced API Development',
                'description' => 'Building RESTful APIs with Laravel',
                'url' => 'https://example.com/videos/api-dev.mp4',
                'thumbnail' => 'https://example.com/thumbs/api.jpg',
                'duration' => 1800, // 30 minutes
                'is_premium' => true,
            ],
            [
                'title' => 'Nature Documentary: Oceans',
                'description' => 'Explore the wonders of ocean life',
                'url' => 'https://example.com/videos/oceans.mp4',
                'thumbnail' => 'https://example.com/thumbs/ocean.jpg',
                'duration' => 3600, // 60 minutes
                'is_premium' => false,
            ],
            [
                'title' => 'Best Football Highlights 2025',
                'description' => 'Top goals and moments from 2025',
                'url' => 'https://example.com/videos/football.mp4',
                'thumbnail' => 'https://example.com/thumbs/football.jpg',
                'duration' => 900, // 15 minutes
                'is_premium' => false,
            ],
            [
                'title' => 'Exclusive: Behind the Scenes',
                'description' => 'Exclusive content for premium users',
                'url' => 'https://example.com/videos/exclusive.mp4',
                'thumbnail' => 'https://example.com/thumbs/exclusive.jpg',
                'duration' => 1500, // 25 minutes
                'is_premium' => true,
            ],
        ];

        foreach ($videos as $videoData) {
            Video::create(array_merge($videoData, [
                'user_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'views' => rand(100, 10000),
                'metadata' => json_encode(['quality' => '1080p', 'language' => 'en'])
            ]));
        }

        echo "Videos created successfully!\n";
    }
}
