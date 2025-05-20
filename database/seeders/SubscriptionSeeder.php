<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptions = [
            [
                'name' => 'Basic',
                'monthly_price' => 9.99,
                'yearly_price' => 99.99,
                'description' => 'Basic subscription plan',
                'boost_likes' => 50,
                'available_posts' => 10,
                'linkedin' => 1,
                'whatsapp' => 0,
                'discount' => 0,
            ],
            [
                'name' => 'Premium',
                'monthly_price' => 19.99,
                'yearly_price' => 199.99,
                'description' => 'Premium subscription plan',
                'boost_likes' => 200,
                'available_posts' => 50,
                'linkedin' => 1,
                'whatsapp' => 1,
                'discount' => 10,
            ],
        ];

        foreach ($subscriptions as $subscription) {
            Subscription::create($subscription);
        }
    }
}