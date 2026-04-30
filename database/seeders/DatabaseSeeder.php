<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Distributor;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Inventory;
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

        // Create sample products
        $products = [
            [
                'name' => 'Vanilla Classic',
                'sku' => 'ICE-VAN-001',
                'description' => 'Rich and creamy vanilla ice cream made with real vanilla beans',
                'category' => 'Classic Flavors',
                'size' => '500ml',
                'price' => 250.00,
                'unit' => 'tub',
                'low_stock_threshold' => 20,
            ],
            [
                'name' => 'Chocolate Fudge',
                'sku' => 'ICE-CHO-002',
                'description' => 'Decadent chocolate ice cream with fudge swirls',
                'category' => 'Classic Flavors',
                'size' => '500ml',
                'price' => 280.00,
                'unit' => 'tub',
                'low_stock_threshold' => 20,
            ],
            [
                'name' => 'Mango Delight',
                'sku' => 'ICE-MAN-003',
                'description' => 'Fresh Alphonso mango ice cream',
                'category' => 'Fruit Flavors',
                'size' => '500ml',
                'price' => 300.00,
                'unit' => 'tub',
                'low_stock_threshold' => 15,
            ],
            [
                'name' => 'Butterscotch',
                'sku' => 'ICE-BUT-004',
                'description' => 'Creamy butterscotch with crunchy praline',
                'category' => 'Classic Flavors',
                'size' => '1L',
                'price' => 450.00,
                'unit' => 'tub',
                'low_stock_threshold' => 10,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Create sample inventory at distributors
        $distributorIds = Distributor::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();

        foreach ($distributorIds as $distributorId) {
            foreach ($productIds as $productId) {
                Inventory::create([
                    'distributor_id' => $distributorId,
                    'product_id' => $productId,
                    'quantity' => rand(10, 100),
                    'reserved_quantity' => rand(0, 5),
                    'location' => 'Main Storage',
                ]);
            }
        }
    }
}