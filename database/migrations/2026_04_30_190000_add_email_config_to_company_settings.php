<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            // SMTP Settings (for sending)
            $table->string('mail_mailer')->nullable()->default('smtp');
            $table->string('mail_host')->nullable();
            $table->integer('mail_port')->nullable()->default(587);
            $table->string('mail_username')->nullable();
            $table->string('mail_password')->nullable();
            $table->string('mail_encryption')->nullable()->default('tls');
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();

            // IMAP Settings (for receiving)
            $table->string('imap_host')->nullable();
            $table->integer('imap_port')->nullable()->default(993);
            $table->string('imap_username')->nullable();
            $table->string('imap_password')->nullable();
            $table->string('imap_encryption')->nullable()->default('ssl');
            $table->string('imap_folder')->nullable()->default('INBOX');

            // Email fetching settings
            $table->boolean('email_fetching_enabled')->default(false);
            $table->timestamp('last_email_fetch_at')->nullable();

            // Cron job settings
            $table->string('cron_token', 64)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn([
                'mail_mailer', 'mail_host', 'mail_port', 'mail_username',
                'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name',
                'imap_host', 'imap_port', 'imap_username', 'imap_password',
                'imap_encryption', 'imap_folder', 'email_fetching_enabled', 'last_email_fetch_at'
            ]);
        });
    }
};
