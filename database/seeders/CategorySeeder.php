<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Movies', 'slug' => 'movies', 'description' => 'Feature films and movies'],
            ['name' => 'TV Shows', 'slug' => 'tv-shows', 'description' => 'Television series and shows'],
            ['name' => 'Documentaries', 'slug' => 'documentaries', 'description' => 'Educational and factual content'],
            ['name' => 'Music', 'slug' => 'music', 'description' => 'Music videos and concerts'],
            ['name' => 'Sports', 'slug' => 'sports', 'description' => 'Sports events and highlights'],
            ['name' => 'Kids', 'slug' => 'kids', 'description' => 'Content for children'],
            ['name' => 'Education', 'slug' => 'education', 'description' => 'Learning and educational content'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        echo "Categories created successfully!\n";
    }
}