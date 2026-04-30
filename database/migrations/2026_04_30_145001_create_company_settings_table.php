<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('company_legal_name')->nullable();
            $table->string('gst_number', 20)->nullable();
            $table->string('pan_number', 20)->nullable();
            $table->text('address');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('country', 100)->default('India');
            $table->string('phone')->nullable();
            $table->string('email');
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            
            // GST Settings
            $table->enum('gst_type', ['b2b', 'b2c', 'none'])->default('b2c');
            $table->decimal('gst_percentage', 5, 2)->default(18.00);
            $table->decimal('cgst_percentage', 5, 2)->default(9.00);
            $table->decimal('sgst_percentage', 5, 2)->default(9.00);
            $table->decimal('igst_percentage', 5, 2)->default(18.00);
            
            // Additional Business Settings
            $table->string('invoice_prefix', 10)->default('INV');
            $table->string('invoice_terms', 50)->default('NET30');
            $table->text('invoice_footer_text')->nullable();
            $table->string('currency', 10)->default('INR');
            $table->string('currency_symbol', 5)->default('₹');
            $table->string('fssai_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_ifsc_code')->nullable();
            $table->string('bank_branch')->nullable();
            $table->text('terms_and_conditions')->nullable();
            
            $table->timestamps();
        });

        // Insert default record
        DB::table('company_settings')->insert([
            'company_name' => 'My Ice Cream Business',
            'company_legal_name' => 'My Ice Cream Business Pvt Ltd',
            'address' => '123 Main Street',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'postal_code' => '400001',
            'email' => 'info@example.com',
            'gst_type' => 'b2c',
            'gst_percentage' => 18.00,
            'cgst_percentage' => 9.00,
            'sgst_percentage' => 9.00,
            'igst_percentage' => 18.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
