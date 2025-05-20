<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'role' => 'admin',
                'post_perm' => true,
                'boost_perm' => false,
                'last_activity' => now()->subDays(2),
                'suspend_end' => null,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'role' => 'user',
                'post_perm' => false,
                'boost_perm' => true,
                'last_activity' => now()->subHours(5),
                'suspend_end' => now()->addDays(7),
            ],
            [
                'name' => 'Alice Johnson',
                'email' => 'alice.johnson@example.com',
                'role' => 'user',
                'post_perm' => true,
                'boost_perm' => true,
                'last_activity' => now()->subDays(1),
                'suspend_end' => null,
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob.wilson@example.com',
                'role' => 'admin',
                'post_perm' => false,
                'boost_perm' => false,
                'last_activity' => now()->subHours(10),
                'suspend_end' => now()->addDays(3),
            ],
            [
                'name' => 'Emma Brown',
                'email' => 'emma.brown@example.com',
                'role' => 'user',
                'post_perm' => true,
                'boost_perm' => false,
                'last_activity' => now(),
                'suspend_end' => null,
            ],
            [
                'name' => 'Michael Lee',
                'email' => 'michael.lee@example.com',
                'role' => 'user',
                'post_perm' => false,
                'boost_perm' => true,
                'last_activity' => now()->subDays(5),
                'suspend_end' => null,
            ],
            [
                'name' => 'Sarah Davis',
                'email' => 'sarah.davis@example.com',
                'role' => 'admin',
                'post_perm' => true,
                'boost_perm' => true,
                'last_activity' => now()->subHours(3),
                'suspend_end' => now()->addDays(10),
            ],
            [
                'name' => 'David Clark',
                'email' => 'david.clark@example.com',
                'role' => 'user',
                'post_perm' => false,
                'boost_perm' => false,
                'last_activity' => now()->subDays(7),
                'suspend_end' => null,
            ],
            [
                'name' => 'Laura Martinez',
                'email' => 'laura.martinez@example.com',
                'role' => 'user',
                'post_perm' => true,
                'boost_perm' => false,
                'last_activity' => now()->subHours(1),
                'suspend_end' => null,
            ],
            [
                'name' => 'James Taylor',
                'email' => 'james.taylor@example.com',
                'role' => 'admin',
                'post_perm' => false,
                'boost_perm' => true,
                'last_activity' => now()->subDays(3),
                'suspend_end' => null,
            ],
        ];

        foreach ($users as $userData) {
            // Skip if email already exists
            if (!User::where('email', $userData['email'])->exists()) {
                User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'role' => $userData['role'],
                    'post_perm' => $userData['post_perm'],
                    'boost_perm' => $userData['boost_perm'],
                    'last_activity' => $userData['last_activity'],
                    'suspend_end' => $userData['suspend_end'],
                    'remember_token' => Str::random(10),
                ]);
            }
        }
    }
}