<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            // Add warehouse reference (Celesty's warehouse where stock is stored)
            $table->foreignId('warehouse_id')->after('id')->constrained()->onDelete('cascade');
            
            // Make distributor_id nullable (only used when tracking distributor orders)
            $table->dropForeign(['distributor_id']);
            $table->foreignId('distributor_id')->nullable()->change()->constrained()->onDelete('set null');
            
            // Update unique constraint to be per warehouse, not per distributor
            $table->dropUnique(['distributor_id', 'product_id']);
            $table->unique(['warehouse_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn('warehouse_id');
            
            $table->dropUnique(['warehouse_id', 'product_id']);
            $table->unique(['distributor_id', 'product_id']);
        });
    }
};
