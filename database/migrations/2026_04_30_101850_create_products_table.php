<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('size');
            $table->decimal('price', 10, 2);
            $table->string('unit'); // kg, liters, pieces, etc.
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('low_stock_threshold')->default(10); // alert when below this
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
