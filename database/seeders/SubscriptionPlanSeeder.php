<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'price' => 0.00,
                'duration_days' => 0, // forever
                'features' => ['SD Quality', 'Limited Content', 'With Ads'],
                'max_video_quality' => 480,
                'offline_download' => false,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'price' => 9.99,
                'duration_days' => 30,
                'features' => ['HD Quality', 'All Content', 'No Ads', '1 Device'],
                'max_video_quality' => 720,
                'offline_download' => false,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'price' => 14.99,
                'duration_days' => 30,
                'features' => ['4K Quality', 'All Content', 'No Ads', '4 Devices', 'Offline Downloads'],
                'max_video_quality' => 2160, // 4K
                'offline_download' => true,
            ],
            [
                'name' => 'Annual Pro',
                'slug' => 'annual-pro',
                'price' => 149.99,
                'duration_days' => 365,
                'features' => ['4K Quality', 'All Content', 'No Ads', '6 Devices', 'Offline Downloads', 'Early Access'],
                'max_video_quality' => 2160,
                'offline_download' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create(array_merge($plan, [
                'is_active' => true,
                'sort_order' => 0
            ]));
        }

        echo "Subscription plans created successfully!\n";
    }
}