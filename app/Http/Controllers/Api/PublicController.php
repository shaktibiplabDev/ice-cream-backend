<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Distributor;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    // Get all active distributors
    public function distributors()
    {
        $distributors = Distributor::where('is_active', true)
            ->select(
                'id',
                'name',
                'contact_person',
                'address',
                'phone',
                'email',
                'latitude',
                'longitude'
            )
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $distributors
        ]);
    }

    // Submit inquiry
    public function submitInquiry(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'requirement' => 'required|string',
        ]);

        $inquiry = Inquiry::create([
            'name' => $validated['name'],
            'business_name' => $validated['business_name'],
            'email' => $validated['email'],
            'requirement' => $validated['requirement'],
            'status' => 'new'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inquiry submitted successfully',
            'data' => $inquiry
        ], 201);
    }
}