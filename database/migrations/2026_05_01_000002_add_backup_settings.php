<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('company_settings', 'backup_days')) {
                $table->integer('backup_days')->default(30)->after('cron_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            if (Schema::hasColumn('company_settings', 'backup_days')) {
                $table->dropColumn('backup_days');
            }
        });
    }
};
