<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('inquiry_number')->nullable()->unique()->after('id');
        });

        DB::table('inquiries')
            ->orderBy('id')
            ->get(['id', 'created_at'])
            ->each(function ($inquiry): void {
                $date = $inquiry->created_at
                    ? \Carbon\Carbon::parse($inquiry->created_at)->format('Ymd')
                    : now()->format('Ymd');

                DB::table('inquiries')
                    ->where('id', $inquiry->id)
                    ->update([
                        'inquiry_number' => 'CLY-' . $date . '-' . str_pad((string) $inquiry->id, 4, '0', STR_PAD_LEFT),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropUnique(['inquiry_number']);
            $table->dropColumn('inquiry_number');
        });
    }
};
