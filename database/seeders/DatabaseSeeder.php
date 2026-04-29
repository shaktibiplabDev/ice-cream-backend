<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Distributor;
use App\Models\Inquiry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@celesty.com',
            'password' => Hash::make('password123'),
        ]);

        // Create sample distributors
        $distributors = [
            [
                'name' => 'Celesty Downtown',
                'address' => '123 Main Street, Downtown',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
            ],
            [
                'name' => 'Celesty Uptown',
                'address' => '456 Broadway, Uptown',
                'latitude' => 40.7580,
                'longitude' => -73.9855,
            ],
            [
                'name' => 'Celesty Westside',
                'address' => '789 West Avenue, Westside',
                'latitude' => 40.7489,
                'longitude' => -73.9680,
            ],
        ];

        foreach ($distributors as $distributor) {
            Distributor::create($distributor);
        }

        // Create sample inquiries
        $inquiries = [
            [
                'name' => 'John Doe',
                'business_name' => 'Sweet Treats Café',
                'email' => 'john@example.com',
                'requirement' => 'Looking to become a distributor for downtown area. Please send me more information.',
                'status' => 'new',
            ],
            [
                'name' => 'Jane Smith',
                'business_name' => 'Ice Cream Paradise',
                'email' => 'jane@example.com',
                'requirement' => 'Interested in bulk orders for our chain of ice cream shops.',
                'status' => 'read',
            ],
        ];

        foreach ($inquiries as $inquiry) {
            Inquiry::create($inquiry);
        }
    }
}