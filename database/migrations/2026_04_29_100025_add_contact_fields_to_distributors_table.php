<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->string('contact_person')->nullable()->after('name');
            $table->string('phone')->nullable()->after('address');
            $table->string('email')->nullable()->after('phone');
            $table->string('website')->nullable()->after('email');
            $table->text('description')->nullable()->after('website');
            $table->string('service_area')->nullable()->after('description');
            $table->string('delivery_capacity')->nullable()->after('service_area');
            $table->boolean('is_active')->default(true)->after('delivery_capacity');
            $table->string('timings')->nullable()->after('is_active');
            $table->string('social_media')->nullable()->after('timings');
        });
    }

    public function down(): void
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->dropColumn([
                'contact_person',
                'phone',
                'email',
                'website',
                'description',
                'service_area',
                'delivery_capacity',
                'is_active',
                'timings',
                'social_media'
            ]);
        });
    }
};