<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'inquiry_number',
        'name',
        'business_name',
        'email',
        'requirement',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Inquiry $inquiry): void {
            if (! $inquiry->inquiry_number) {
                $inquiry->inquiry_number = static::generateInquiryNumber();
            }
        });
    }

    public static function generateInquiryNumber(): string
    {
        do {
            $number = 'CLY-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
        } while (static::where('inquiry_number', $number)->exists());

        return $number;
    }

    public function messages(): HasMany
    {
        return $this->hasMany(InquiryMessage::class)->oldest();
    }

    public function displayNumber(): string
    {
        return $this->inquiry_number ?: '#INQ-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }
}
