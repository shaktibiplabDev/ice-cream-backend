<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\InquiryRequest;
use App\Http\Controllers\Controller;
use App\Mail\AdminInquiryReceivedMail;
use App\Mail\InquiryReceivedMail;
use App\Models\Distributor;
use App\Models\Inquiry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
    public function submitInquiry(InquiryRequest $request)
    {
        $validated = $request->validated();

        $inquiry = Inquiry::create([
            'name' => $validated['name'],
            'business_name' => $validated['business_name'] ?? null,
            'email' => $validated['email'],
            'requirement' => $validated['requirement'],
            'status' => 'new',
        ]);

        $inquiry->messages()->create([
            'direction' => 'inbound',
            'sender_name' => $inquiry->name,
            'sender_email' => $inquiry->email,
            'recipient_email' => config('mail.inquiry_inbox'),
            'subject' => 'New inquiry ' . $inquiry->displayNumber(),
            'body' => $inquiry->requirement,
            'sent_at' => $inquiry->created_at,
        ]);

        try {
            Mail::to($inquiry->email)->send(new InquiryReceivedMail($inquiry));

            if (config('mail.inquiry_inbox')) {
                Mail::to(config('mail.inquiry_inbox'))->send(new AdminInquiryReceivedMail($inquiry));
            }
        } catch (\Throwable $exception) {
            Log::warning('Inquiry email could not be sent.', [
                'inquiry_id' => $inquiry->id,
                'message' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Inquiry submitted successfully',
            'data' => [
                'id' => $inquiry->id,
                'inquiry_number' => $inquiry->displayNumber(),
                'status' => $inquiry->status,
            ],
        ], 201);
    }
}
