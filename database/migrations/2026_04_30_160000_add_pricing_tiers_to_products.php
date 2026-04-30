<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Rename existing price to mr_price (Maximum Retail Price - customer price)
            $table->renameColumn('price', 'mrp_price');
            
            // Add distributor and retailer prices
            $table->decimal('distributor_price', 10, 2)->default(0)->after('mrp_price');
            $table->decimal('retailer_price', 10, 2)->default(0)->after('distributor_price');
            
            // Add category reference
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'distributor_price', 'retailer_price']);
            $table->renameColumn('mrp_price', 'price');
        });
    }
};
