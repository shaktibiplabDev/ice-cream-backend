<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_legal_name',
        'gst_number',
        'pan_number',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'email',
        'website',
        'logo_path',
        'gst_type',
        'gst_percentage',
        'cgst_percentage',
        'sgst_percentage',
        'igst_percentage',
        'invoice_prefix',
        'invoice_terms',
        'invoice_footer_text',
        'currency',
        'currency_symbol',
        'fssai_number',
        'bank_name',
        'bank_account_number',
        'bank_ifsc_code',
        'bank_branch',
        'terms_and_conditions',
    ];

    protected $casts = [
        'gst_percentage' => 'decimal:2',
        'cgst_percentage' => 'decimal:2',
        'sgst_percentage' => 'decimal:2',
        'igst_percentage' => 'decimal:2',
    ];

    /**
     * Get the first (and only) settings record
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate(
            [],
            [
                'company_name' => 'My Ice Cream Business',
                'address' => 'Business Address',
                'email' => 'info@example.com',
                'gst_type' => 'b2c',
                'gst_percentage' => 18.00,
                'cgst_percentage' => 9.00,
                'sgst_percentage' => 9.00,
                'igst_percentage' => 18.00,
                'currency' => 'INR',
                'currency_symbol' => '₹',
            ]
        );
    }

    /**
     * Check if GST is enabled
     */
    public function isGstEnabled(): bool
    {
        return $this->gst_type !== 'none' && $this->gst_percentage > 0;
    }

    /**
     * Check if B2B GST (with invoice/IGST)
     */
    public function isB2B(): bool
    {
        return $this->gst_type === 'b2b';
    }

    /**
     * Check if B2C GST (local CGST+SGST)
     */
    public function isB2C(): bool
    {
        return $this->gst_type === 'b2c';
    }

    /**
     * Get GST breakdown for amount
     */
    public function calculateGst(float $amount): array
    {
        if (!$this->isGstEnabled()) {
            return [
                'cgst' => 0,
                'sgst' => 0,
                'igst' => 0,
                'total_gst' => 0,
                'total_with_gst' => $amount,
            ];
        }

        if ($this->isB2B()) {
            // B2B: IGST
            $igst = $amount * ($this->igst_percentage / 100);
            return [
                'cgst' => 0,
                'sgst' => 0,
                'igst' => round($igst, 2),
                'total_gst' => round($igst, 2),
                'total_with_gst' => round($amount + $igst, 2),
            ];
        }

        // B2C: CGST + SGST
        $cgst = $amount * ($this->cgst_percentage / 100);
        $sgst = $amount * ($this->sgst_percentage / 100);
        
        return [
            'cgst' => round($cgst, 2),
            'sgst' => round($sgst, 2),
            'igst' => 0,
            'total_gst' => round($cgst + $sgst, 2),
            'total_with_gst' => round($amount + $cgst + $sgst, 2),
        ];
    }
}
