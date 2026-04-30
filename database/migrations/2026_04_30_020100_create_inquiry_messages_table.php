<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiry_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained()->cascadeOnDelete();
            $table->enum('direction', ['inbound', 'outbound']);
            $table->string('sender_name')->nullable();
            $table->string('sender_email');
            $table->string('recipient_email');
            $table->string('subject');
            $table->text('body');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        DB::table('inquiries')
            ->orderBy('id')
            ->get()
            ->each(function ($inquiry): void {
                DB::table('inquiry_messages')->insert([
                    'inquiry_id' => $inquiry->id,
                    'direction' => 'inbound',
                    'sender_name' => $inquiry->name,
                    'sender_email' => $inquiry->email,
                    'recipient_email' => config('mail.inquiry_inbox'),
                    'subject' => 'New inquiry ' . ($inquiry->inquiry_number ?: '#INQ-' . str_pad((string) $inquiry->id, 4, '0', STR_PAD_LEFT)),
                    'body' => $inquiry->requirement,
                    'sent_at' => $inquiry->created_at,
                    'created_at' => $inquiry->created_at,
                    'updated_at' => $inquiry->updated_at,
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiry_messages');
    }
};
