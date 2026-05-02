<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Distributor;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Warehouse;
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

        // Create Celesty Warehouses (storage facilities)
        $warehouses = [
            [
                'name' => 'Celesty Mumbai Central Warehouse',
                'code' => 'WH-MUM-001',
                'address' => '123 Industrial Area, Andheri East',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'country' => 'India',
                'postal_code' => '400069',
                'phone' => '+91 22 1234 5678',
                'email' => 'mumbai.wh@celesty.com',
                'manager_name' => 'Rajesh Kumar',
                'latitude' => 19.0760,
                'longitude' => 72.8777,
                'is_active' => true,
            ],
            [
                'name' => 'Celesty Delhi NCR Warehouse',
                'code' => 'WH-DEL-001',
                'address' => '456 Logistics Park, Gurugram',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'country' => 'India',
                'postal_code' => '122001',
                'phone' => '+91 124 1234 5678',
                'email' => 'delhi.wh@celesty.com',
                'manager_name' => 'Priya Sharma',
                'latitude' => 28.4595,
                'longitude' => 77.0266,
                'is_active' => true,
            ],
            [
                'name' => 'Celesty Bangalore Warehouse',
                'code' => 'WH-BLR-001',
                'address' => '789 Electronic City Phase 1',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'country' => 'India',
                'postal_code' => '560100',
                'phone' => '+91 80 1234 5678',
                'email' => 'bangalore.wh@celesty.com',
                'manager_name' => 'Arun Nair',
                'latitude' => 12.9716,
                'longitude' => 77.5946,
                'is_active' => true,
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }

        // Create sample distributors (these are customers who order from Celesty)
        $distributors = [
            [
                'name' => 'Sweet Treats Café',
                'address' => '45 Marine Drive, Mumbai',
                'contact_person' => 'John Doe',
                'phone' => '+91 98765 43210',
                'email' => 'john@sweettreats.com',
                'service_area' => 'South Mumbai',
                'latitude' => 18.9442,
                'longitude' => 72.8236,
                'is_active' => true,
            ],
            [
                'name' => 'Ice Cream Paradise',
                'address' => '78 Connaught Place, Delhi',
                'contact_person' => 'Jane Smith',
                'phone' => '+91 98765 43211',
                'email' => 'jane@icecreamparadise.com',
                'service_area' => 'Central Delhi',
                'latitude' => 28.6315,
                'longitude' => 77.2167,
                'is_active' => true,
            ],
            [
                'name' => 'Frozen Delights',
                'address' => '12 Koramangala, Bangalore',
                'contact_person' => 'Mike Johnson',
                'phone' => '+91 98765 43212',
                'email' => 'mike@frozendelights.com',
                'service_area' => 'Bangalore Central',
                'latitude' => 12.9352,
                'longitude' => 77.6245,
                'is_active' => true,
            ],
        ];

        foreach ($distributors as $distributor) {
            Distributor::create($distributor);
        }

        // Create categories first (required for products)
        $categories = [
            ['name' => 'Ice Cream', 'slug' => 'ice-cream', 'icon' => '🍦', 'description' => 'Premium ice cream flavors', 'sort_order' => 1, 'is_active' => true],
            ['name' => 'Frozen Yogurt', 'slug' => 'frozen-yogurt', 'icon' => '🥛', 'description' => 'Healthy frozen yogurt', 'sort_order' => 2, 'is_active' => true],
            ['name' => 'Sorbet', 'slug' => 'sorbet', 'icon' => '🍧', 'description' => 'Fruit-based sorbets', 'sort_order' => 3, 'is_active' => true],
            ['name' => 'Kulfi', 'slug' => 'kulfi', 'icon' => '🍨', 'description' => 'Traditional Indian kulfi', 'sort_order' => 4, 'is_active' => true],
            ['name' => 'Family Pack', 'slug' => 'family-pack', 'icon' => '🎁', 'description' => 'Large family packs', 'sort_order' => 5, 'is_active' => true],
        ];

        $createdCategories = [];
        foreach ($categories as $category) {
            $createdCategories[$category['slug']] = Category::create($category);
        }

        // Create sample inquiries
        $inquiries = [
            [
                'name' => 'Amit Patel',
                'business_name' => 'Patel Sweets',
                'email' => 'amit@patelsweets.com',
                'requirement' => 'Looking to become a distributor for Ahmedabad area. We have 5 retail outlets and need bulk supply.',
                'status' => 'new',
                'inquiry_number' => 'INQ-001',
            ],
            [
                'name' => 'Sarah Williams',
                'business_name' => 'City Ice Cream Co.',
                'email' => 'sarah@cityicecream.com',
                'requirement' => 'Interested in wholesale pricing for our chain of 10 ice cream parlors across Pune.',
                'status' => 'read',
                'inquiry_number' => 'INQ-002',
            ],
        ];

        foreach ($inquiries as $inquiry) {
            Inquiry::create($inquiry);
        }

        // Create sample products with category_id references
        $iceCreamCategoryId = $createdCategories['ice-cream']->id;

        $products = [
            [
                'name' => 'Vanilla Classic',
                'sku' => 'ICE-VAN-001',
                'description' => 'Rich and creamy vanilla ice cream made with real vanilla beans',
                'category_id' => $iceCreamCategoryId,
                'size' => '500ml',
                'mrp_price' => 250.00,
                'distributor_price' => 200.00,
                'retailer_price' => 225.00,
                'unit' => 'tub',
                'low_stock_threshold' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Chocolate Fudge',
                'sku' => 'ICE-CHO-002',
                'description' => 'Decadent chocolate ice cream with fudge swirls',
                'category_id' => $iceCreamCategoryId,
                'size' => '500ml',
                'mrp_price' => 280.00,
                'distributor_price' => 224.00,
                'retailer_price' => 252.00,
                'unit' => 'tub',
                'low_stock_threshold' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Mango Delight',
                'sku' => 'ICE-MAN-003',
                'description' => 'Fresh Alphonso mango ice cream',
                'category_id' => $iceCreamCategoryId,
                'size' => '500ml',
                'mrp_price' => 300.00,
                'distributor_price' => 240.00,
                'retailer_price' => 270.00,
                'unit' => 'tub',
                'low_stock_threshold' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'Butterscotch Bliss',
                'sku' => 'ICE-BUT-004',
                'description' => 'Creamy butterscotch with crunchy praline',
                'category_id' => $iceCreamCategoryId,
                'size' => '1L',
                'mrp_price' => 450.00,
                'distributor_price' => 360.00,
                'retailer_price' => 405.00,
                'unit' => 'tub',
                'low_stock_threshold' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Strawberry Swirl',
                'sku' => 'ICE-STR-005',
                'description' => 'Fresh strawberry ice cream with real fruit chunks',
                'category_id' => $iceCreamCategoryId,
                'size' => '500ml',
                'mrp_price' => 290.00,
                'distributor_price' => 232.00,
                'retailer_price' => 261.00,
                'unit' => 'tub',
                'low_stock_threshold' => 35,
                'is_active' => true,
            ],
            [
                'name' => 'Kesar Pista',
                'sku' => 'ICE-KES-006',
                'description' => 'Traditional Indian flavor with saffron and pistachios',
                'category_id' => $iceCreamCategoryId,
                'size' => '500ml',
                'mrp_price' => 350.00,
                'distributor_price' => 280.00,
                'retailer_price' => 315.00,
                'unit' => 'tub',
                'low_stock_threshold' => 25,
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Create inventory at warehouses (Celesty's stock)
        $warehouseIds = Warehouse::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();
        $locations = ['Cold Storage A', 'Cold Storage B', 'Freezer Zone 1', 'Freezer Zone 2', 'Main Warehouse'];

        foreach ($warehouseIds as $warehouseId) {
            foreach ($productIds as $productId) {
                Inventory::create([
                    'warehouse_id' => $warehouseId,
                    'product_id' => $productId,
                    'distributor_id' => null, // No distributor assigned yet
                    'quantity' => rand(50, 500),
                    'reserved_quantity' => rand(0, 20),
                    'location' => $locations[array_rand($locations)],
                ]);
            }
        }
    }
}