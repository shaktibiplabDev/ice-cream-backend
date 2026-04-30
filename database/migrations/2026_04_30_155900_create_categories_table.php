<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default ice cream categories
        DB::table('categories')->insert([
            [
                'name' => 'Cups & Cones',
                'slug' => 'cups-cones',
                'description' => 'Individual serving ice creams in cups and cones',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Family Packs',
                'slug' => 'family-packs',
                'description' => 'Larger packs for family sharing',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sticks & Bars',
                'slug' => 'sticks-bars',
                'description' => 'Ice cream on sticks and candy bars',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sundaes & Specials',
                'slug' => 'sundaes-specials',
                'description' => 'Premium sundaes and special flavors',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bulk/Party Packs',
                'slug' => 'bulk-party-packs',
                'description' => 'Large quantities for events and parties',
                'sort_order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
