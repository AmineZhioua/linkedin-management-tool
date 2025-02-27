<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
       public function run()
    {
        Subscription::create([
            'name' => 'Basic Plan',
            'price' => 0.00,
            'description' => 'Offert pour ton abonnement',
            'features' => json_encode(['Hausse de 100 likes', 'Accès limité']),
        ]);

        Subscription::create([
            'name' => 'Premium Plan',
            'price' => 9.99,
            'description' => 'Accès premium avec plus de fonctionnalités',
            'features' => json_encode(['Hausse de 500 likes', 'Accès illimité', 'Support prioritaire']),
        ]);
    }
}
