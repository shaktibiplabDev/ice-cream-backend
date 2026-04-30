<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('company_settings', 'last_email_fetch_at')) {
                $table->timestamp('last_email_fetch_at')->nullable()->after('email_fetching_enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            if (Schema::hasColumn('company_settings', 'last_email_fetch_at')) {
                $table->dropColumn('last_email_fetch_at');
            }
        });
    }
};
