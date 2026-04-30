<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
            $table->enum('type', ['inbox', 'sent', 'draft'])->default('sent');
            $table->string('from_name');
            $table->string('from_email');
            $table->string('to_name')->nullable();
            $table->string('to_email');
            $table->string('cc')->nullable();
            $table->string('bcc')->nullable();
            $table->string('subject');
            $table->text('body');
            $table->text('body_html')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_starred')->default(false);
            $table->boolean('is_important')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->json('attachments')->nullable();
            $table->string('message_id')->nullable();
            $table->string('in_reply_to')->nullable();
            $table->timestamps();

            $table->index(['type', 'created_by']);
            $table->index(['is_read', 'type']);
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
