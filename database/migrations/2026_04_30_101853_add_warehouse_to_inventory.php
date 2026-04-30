<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            // First drop foreign key constraint on distributor_id (this drops the index too)
            $table->dropForeign(['distributor_id']);

            // Now add warehouse_id column
            $table->foreignId('warehouse_id')->after('id')->nullable()->constrained()->onDelete('cascade');

            // Make distributor_id nullable
            $table->unsignedBigInteger('distributor_id')->nullable()->change();

            // Re-add foreign key for distributor_id (nullable, set null on delete)
            $table->foreign('distributor_id')
                  ->references('id')
                  ->on('distributors')
                  ->onDelete('set null');

            // Add unique constraint for warehouse + product
            $table->unique(['warehouse_id', 'product_id'], 'inventory_wh_prod_unique');
        });
    }

    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            // Drop warehouse foreign key and unique constraint
            $table->dropForeign(['warehouse_id']);
            $table->dropUnique('inventory_wh_prod_unique');
            $table->dropColumn('warehouse_id');

            // Restore distributor_id as not nullable and re-add unique constraint
            $table->dropForeign(['distributor_id']);
            $table->unsignedBigInteger('distributor_id')->nullable(false)->change();
            $table->foreign('distributor_id')
                  ->references('id')
                  ->on('distributors')
                  ->onDelete('cascade');
            $table->unique(['distributor_id', 'product_id'], 'inventory_dist_prod_unique');
        });
    }
};
