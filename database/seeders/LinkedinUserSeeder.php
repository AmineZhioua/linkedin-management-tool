<?php

namespace Database\Seeders;

use App\Models\LinkedinUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LinkedinUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LinkedinUser::create([
            'user_id' => 1,
            'linkedin_id' => 'BZX1Y2Z3A4',
            'linkedin_token' => 'your_access_token',
            'expire_at' => now()->addMonths(2),
            'linkedin_refresh_token' => 'your_refresh_token',
            'linkedin_firstname' => 'John',
            'linkedin_lastname' => 'Doe',
            'linkedin_picture' => 'https://example.com/profile.jpg',
        ]);

        LinkedinUser::create([
            'user_id' => 1,
            'linkedin_id' => 'BZW1T2S3A9',
            'linkedin_token' => 'your_access_token',
            'expire_at' => now()->addMonths(2),
            'linkedin_refresh_token' => 'your_refresh_token',
            'linkedin_firstname' => 'Ahmed',
            'linkedin_lastname' => 'Omry',
            'linkedin_picture' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZbNCYpIFaH0SQOgckMP90UJkwCIOoh6AJtQ&s',
        ]);

        LinkedinUser::create([
            'user_id' => 1,
            'linkedin_id' => 'VYP5T2Z3L9',
            'linkedin_token' => 'your_access_token',
            'expire_at' => now()->addMonths(2),
            'linkedin_refresh_token' => 'your_refresh_token',
            'linkedin_firstname' => 'Med',
            'linkedin_lastname' => 'Welfki',
            'linkedin_picture' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRL2de_EUA1aFedrjCcpFf8FbMObTcG3BkGcQ&s',
        ]);
    }
}
