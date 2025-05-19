<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Subscription;
use App\Models\UserSubscription;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserSubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch existing user and subscription IDs
        $userIds = User::pluck('id')->toArray();
        $subscriptionIds = Subscription::pluck('id')->toArray();

        // Check if there are users and subscriptions
        if (empty($userIds) || empty($subscriptionIds)) {
            throw new \Exception('Users or Subscriptions table is empty. Please seed Users and Subscriptions first.');
        }

        // Create 20 UserSubscription records
        for ($i = 0; $i < 20; $i++) {
            UserSubscription::create([
                'user_id' => $userIds[array_rand($userIds)],
                'subscription_id' => $subscriptionIds[array_rand($subscriptionIds)],
                'pricing_mode' => array_rand(['monthly' => 1, 'yearly' => 1]),
                'date_expiration' => Carbon::now()->addDays(rand(1, 365)),
            ]);
        }
    }
}