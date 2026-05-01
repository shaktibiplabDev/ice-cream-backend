<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DistributorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'gst_number' => 'nullable|string|max:20',
            'business_type' => 'nullable|in:b2b,b2c',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'service_area' => 'nullable|string|max:500',
            'delivery_capacity' => 'nullable|string|max:100',
            'is_active' => 'required|boolean',
            'timings' => 'nullable|string|max:255',
            'social_media' => 'nullable|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
