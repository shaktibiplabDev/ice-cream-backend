<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->string('gst_number', 20)->nullable()->after('email');
            $table->enum('business_type', ['b2b', 'b2c'])->default('b2c')->after('gst_number');
        });
    }

    public function down(): void
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->dropColumn(['gst_number', 'business_type']);
        });
    }
};
