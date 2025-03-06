<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed some test coupons
        Coupon::insert([
            [
                'code' => 'WELCOME10',
                'discount' => 10, // 10% discount
                'type' => 'percentage', // Can be 'percentage' or 'fixed'
                'expires_at' => Carbon::now()->addDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SAVE50',
                'discount' => 50, // €50 discount
                'type' => 'fixed',
                'expires_at' => Carbon::now()->addDays(60),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'FREEMONTH',
                'discount' => 100, // 100% discount (free)
                'type' => 'percentage',
                'expires_at' => Carbon::now()->addDays(15),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
