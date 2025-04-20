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
            'name' => 'LinkedIn Plan',
            'monthly_price' => 22,
            'yearly_price' => 120,
            'description' => 'Offert pour ton abonnement',
            'features' => json_encode(['Hausse de 100 likes', 'Accès limité', 'Support prioritaire']),
            'linkedin' => true,
            'whatsapp' => false,
            'discount' => 0,
        ]);

        Subscription::create([
            'name' => 'WhatsApp Plan',
            'monthly_price' => 19,
            'yearly_price' => 59.99,
            'description' => 'Accès premium avec plus de fonctionnalités',
            'features' => json_encode(['Hausse de 500 likes', 'Accès illimité', 'Support prioritaire']),
            'linkedin' => false,
            'whatsapp' => true,
            'discount' => 0,
        ]);

        Subscription::create([
            'name' => 'WhatsApp & LinkedIn Plan',
            'monthly_price' => 50,
            'yearly_price' => 150,
            'description' => 'Accès premium avec plus de fonctionnalités',
            'features' => json_encode(['Hausse de 500 likes', 'Accès illimité', 'Support prioritaire']),
            'linkedin' => true,
            'whatsapp' => true,
            'discount' => 10,
        ]);
    }
}
