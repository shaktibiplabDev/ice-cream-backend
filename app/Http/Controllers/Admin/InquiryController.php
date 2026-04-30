<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InquiryReplyMail;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InquiryController extends Controller
{
    public function index()
    {
        $inquiries = Inquiry::latest()->paginate(20);
        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function show($id)
    {
        $inquiry = Inquiry::with('messages')->findOrFail($id);
        
        if ($inquiry->status == 'new') {
            $inquiry->update(['status' => 'read']);
            $inquiry->status = 'read';
        }
        
        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function updateStatus(Request $request, $id)
    {
        $inquiry = Inquiry::findOrFail($id);
        $inquiry->update(['status' => $request->status]);
        
        return redirect()->back()->with('success', 'Status updated successfully');
    }

    public function reply(Request $request, $id)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
        ]);

        $inquiry = Inquiry::findOrFail($id);
        $admin = auth('admin')->user();

        $message = $inquiry->messages()->create([
            'direction' => 'outbound',
            'sender_name' => $admin?->name ?? config('mail.from.name'),
            'sender_email' => $admin?->email ?? config('mail.from.address'),
            'recipient_email' => $inquiry->email,
            'subject' => $validated['subject'],
            'body' => $validated['body'],
        ]);

        try {
            Mail::to($inquiry->email)->send(new InquiryReplyMail($inquiry, $message));

            $message->update(['sent_at' => now()]);
            $inquiry->update(['status' => 'replied']);

            return redirect()
                ->route('admin.inquiries.show', $inquiry->id)
                ->with('success', 'Reply sent to ' . $inquiry->email);
        } catch (\Throwable $exception) {
            Log::warning('Inquiry reply email could not be sent.', [
                'inquiry_id' => $inquiry->id,
                'message_id' => $message->id,
                'error' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('admin.inquiries.show', $inquiry->id)
                ->withErrors(['reply' => 'Reply saved, but the email could not be sent. Please check your mail settings.']);
        }
    }

    public function destroy($id)
    {
        $inquiry = Inquiry::findOrFail($id);
        $inquiry->delete();
        
        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry deleted successfully');
    }
}
