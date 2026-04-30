<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class MailController extends Controller
{
    public function inbox()
    {
        $emails = Email::inbox(auth('admin')->id())
            ->orderBy('sent_at', 'desc')
            ->paginate(20);

        $unreadCount = Email::inbox(auth('admin')->id())->unread()->count();

        return view('admin.mail.inbox', compact('emails', 'unreadCount'));
    }

    public function sent()
    {
        $emails = Email::sent(auth('admin')->id())
            ->orderBy('sent_at', 'desc')
            ->paginate(20);

        return view('admin.mail.sent', compact('emails'));
    }

    public function drafts()
    {
        $emails = Email::drafts(auth('admin')->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.mail.drafts', compact('emails'));
    }

    public function starred()
    {
        $emails = Email::where('created_by', auth('admin')->id())
            ->starred()
            ->orderBy('sent_at', 'desc')
            ->paginate(20);

        return view('admin.mail.starred', compact('emails'));
    }

    public function compose()
    {
        $settings = CompanySetting::getSettings();
        return view('admin.mail.compose', compact('settings'));
    }

    public function show(Email $email)
    {
        if ($email->created_by !== auth('admin')->id()) {
            abort(403);
        }

        if ($email->type === 'inbox' && !$email->is_read) {
            $email->markAsRead();
        }

        $settings = CompanySetting::getSettings();
        
        // Get conversation thread
        $conversation = collect([]);
        if ($email->in_reply_to) {
            $conversation = Email::where('created_by', auth('admin')->id())
                ->where(function ($query) use ($email) {
                    $query->where('in_reply_to', $email->in_reply_to)
                          ->orWhere('message_id', $email->in_reply_to)
                          ->orWhere('id', $email->in_reply_to);
                })
                ->orWhere('id', $email->id)
                ->orderBy('sent_at', 'asc')
                ->get();
        }

        // Check if linked to inquiry
        $linkedInquiry = \App\Models\Inquiry::where('email', $email->from_email)
            ->orWhere('email', $email->to_email)
            ->first();

        return view('admin.mail.show', compact('email', 'settings', 'conversation', 'linkedInquiry'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'to_email' => 'required|email',
            'to_name' => 'nullable|string|max:255',
            'cc' => 'nullable|string',
            'bcc' => 'nullable|string',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240',
            'send_now' => 'boolean',
        ]);

        $settings = CompanySetting::getSettings();

        // Handle attachments
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('email-attachments', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ];
            }
        }

        $email = Email::create([
            'created_by' => auth('admin')->id(),
            'type' => $request->boolean('send_now') ? 'sent' : 'draft',
            'from_name' => $settings->company_name,
            'from_email' => $settings->email,
            'to_email' => $validated['to_email'],
            'to_name' => $validated['to_name'] ?? null,
            'cc' => $validated['cc'] ?? null,
            'bcc' => $validated['bcc'] ?? null,
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'body_html' => nl2br(e($validated['body'])),
            'sent_at' => $request->boolean('send_now') ? now() : null,
            'attachments' => $attachments,
        ]);

        // Actually send the email if requested
        if ($request->boolean('send_now')) {
            try {
                Mail::raw($validated['body'], function($message) use ($validated, $settings, $attachments) {
                    $message->from($settings->email, $settings->company_name)
                        ->to($validated['to_email'], $validated['to_name'] ?? null)
                        ->subject($validated['subject']);

                    if (!empty($validated['cc'])) {
                        $message->cc($this->parseEmails($validated['cc']));
                    }

                    if (!empty($validated['bcc'])) {
                        $message->bcc($this->parseEmails($validated['bcc']));
                    }

                    foreach ($attachments as $attachment) {
                        $message->attach(storage_path('app/public/' . $attachment['path']), [
                            'as' => $attachment['name'],
                        ]);
                    }
                });

                return redirect()->route('admin.mail.sent')
                    ->with('success', 'Email sent successfully');
            } catch (\Exception $e) {
                $email->update(['type' => 'draft']);
                return redirect()->back()
                    ->with('error', 'Failed to send email: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.mail.drafts')
            ->with('success', 'Draft saved successfully');
    }

    public function destroy(Email $email)
    {
        if ($email->created_by !== auth('admin')->id()) {
            abort(403);
        }

        // Delete attachments
        if ($email->attachments) {
            foreach ($email->attachments as $attachment) {
                if (isset($attachment['path']) && Storage::exists($attachment['path'])) {
                    Storage::delete($attachment['path']);
                }
            }
        }

        $email->delete();

        return redirect()->back()
            ->with('success', 'Email deleted successfully');
    }

    public function toggleStar(Email $email)
    {
        if ($email->created_by !== auth('admin')->id()) {
            abort(403);
        }

        $email->update(['is_starred' => !$email->is_starred]);

        return response()->json([
            'success' => true,
            'is_starred' => $email->is_starred,
        ]);
    }

    public function toggleImportant(Email $email)
    {
        if ($email->created_by !== auth('admin')->id()) {
            abort(403);
        }

        $email->update(['is_important' => !$email->is_important]);

        return response()->json([
            'success' => true,
            'is_important' => $email->is_important,
        ]);
    }

    private function parseEmails(string $emails): array
    {
        return array_map('trim', explode(',', $emails));
    }

    /**
     * Show reply form for an email
     */
    public function reply(Email $email)
    {
        if ($email->created_by !== auth('admin')->id()) {
            abort(403);
        }

        $settings = CompanySetting::getSettings();
        
        // Check if this email is linked to an inquiry
        $linkedInquiry = \App\Models\Inquiry::where('email', $email->from_email)->first();

        return view('admin.mail.reply', compact('email', 'settings', 'linkedInquiry'));
    }

    /**
     * Send reply to an email
     */
    public function sendReply(Request $request, Email $email)
    {
        if ($email->created_by !== auth('admin')->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $settings = CompanySetting::getSettings();

        // Handle attachments
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('email-attachments', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ];
            }
        }

        // Create sent email record
        $replyEmail = Email::create([
            'created_by' => auth('admin')->id(),
            'type' => 'sent',
            'from_name' => $settings->company_name,
            'from_email' => $settings->email,
            'to_email' => $email->from_email,
            'to_name' => $email->from_name,
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'body_html' => nl2br(e($validated['body'])),
            'sent_at' => now(),
            'attachments' => $attachments,
            'in_reply_to' => $email->message_id ?? $email->id,
        ]);

        // Link to inquiry if exists
        $inquiry = \App\Models\Inquiry::where('email', $email->from_email)->first();
        if ($inquiry) {
            \App\Models\InquiryMessage::create([
                'inquiry_id' => $inquiry->id,
                'direction' => 'outgoing',
                'sender_name' => $settings->company_name,
                'sender_email' => $settings->email,
                'content' => $validated['body'],
                'sent_at' => now(),
            ]);
        }

        // Actually send the email
        try {
            Mail::raw($validated['body'], function($message) use ($email, $settings, $validated, $attachments) {
                $message->from($settings->email, $settings->company_name)
                    ->to($email->from_email, $email->from_name)
                    ->subject($validated['subject']);

                foreach ($attachments as $attachment) {
                    $message->attach(storage_path('app/public/' . $attachment['path']), [
                        'as' => $attachment['name'],
                    ]);
                }
            });

            return redirect()->route('admin.mail.sent')
                ->with('success', 'Reply sent successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to send reply: ' . $e->getMessage());
        }
    }
}
