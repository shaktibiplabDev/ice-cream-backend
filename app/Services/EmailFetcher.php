<?php

namespace App\Services;

use App\Models\Email;
use App\Models\CompanySetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EmailFetcher
{
    /** @var resource|null */
    private $connection = null;
    private CompanySetting $settings;

    public function __construct()
    {
        $this->settings = CompanySetting::getSettings();
    }

    /**
     * Get IMAP setting from env or database
     */
    private function getImapHost(): ?string
    {
        return env('IMAP_HOST', $this->settings->imap_host);
    }

    private function getImapPort(): int
    {
        return (int) env('IMAP_PORT', $this->settings->imap_port ?? 993);
    }

    private function getImapUsername(): ?string
    {
        return env('IMAP_USERNAME', $this->settings->imap_username);
    }

    private function getImapPassword(): ?string
    {
        return env('IMAP_PASSWORD', $this->settings->imap_password);
    }

    private function getImapEncryption(): string
    {
        return env('IMAP_ENCRYPTION', $this->settings->imap_encryption ?? 'ssl');
    }

    private function getImapFolder(): string
    {
        return env('IMAP_FOLDER', $this->settings->imap_folder ?? 'INBOX');
    }

    /**
     * Check if email fetching is enabled
     */
    private function isEnabled(): bool
    {
        return env('IMAP_ENABLED', $this->settings->email_fetching_enabled ?? false);
    }

    /**
     * Fetch emails from IMAP server
     */
    public function fetch(): array
    {
        if (!$this->isEnabled()) {
            return ['status' => 'disabled', 'message' => 'Email fetching is disabled'];
        }

        if (!$this->connect()) {
            return ['status' => 'error', 'message' => 'Failed to connect to IMAP server'];
        }

        $stats = ['fetched' => 0, 'errors' => 0, 'messages' => []];

        try {
            $folder = $this->getImapFolder();
            $imapPath = $this->buildImapPath($folder);

            $mailbox = imap_open($imapPath, $this->getImapUsername(), $this->getImapPassword());

            if (!$mailbox) {
                throw new \Exception('Could not open mailbox: ' . imap_last_error());
            }

            // Search for unseen messages (new emails)
            $emailIds = imap_search($mailbox, 'UNSEEN');

            if ($emailIds) {
                foreach ($emailIds as $emailId) {
                    try {
                        $this->processEmail($mailbox, $emailId);
                        $stats['fetched']++;
                    } catch (\Exception $e) {
                        $stats['errors']++;
                        Log::error('Failed to process email', ['email_id' => $emailId, 'error' => $e->getMessage()]);
                    }
                }
            }

            // Also check recent emails (last 24 hours) that might have been missed
            $since = date('d-M-Y', strtotime('-1 day'));
            $recentIds = imap_search($mailbox, "SINCE \"{$since}\"");

            if ($recentIds) {
                foreach ($recentIds as $emailId) {
                    // Skip if already processed
                    $messageId = imap_headerinfo($mailbox, $emailId)->message_id ?? null;
                    if ($messageId && Email::where('message_id', $messageId)->exists()) {
                        continue;
                    }

                    try {
                        $this->processEmail($mailbox, $emailId);
                        $stats['fetched']++;
                    } catch (\Exception $e) {
                        $stats['errors']++;
                        Log::error('Failed to process recent email', ['email_id' => $emailId, 'error' => $e->getMessage()]);
                    }
                }
            }

            imap_close($mailbox);

            // Update last fetch timestamp
            $this->settings->update(['last_email_fetch_at' => now()]);

            $stats['status'] = 'success';
            $stats['message'] = "Fetched {$stats['fetched']} emails, {$stats['errors']} errors";

        } catch (\Exception $e) {
            Log::error('Email fetch failed', ['error' => $e->getMessage()]);
            $stats['status'] = 'error';
            $stats['message'] = $e->getMessage();
        }

        $this->disconnect();
        return $stats;
    }

    /**
     * Process a single email
     */
    private function processEmail($mailbox, int $emailId): void
    {
        $header = imap_headerinfo($mailbox, $emailId);
        $structure = imap_fetchstructure($mailbox, $emailId);

        $messageId = $header->message_id ?? null;

        // Skip if already exists
        if ($messageId && Email::where('message_id', $messageId)->exists()) {
            return;
        }

        // Parse sender
        $from = $header->from[0] ?? null;
        $fromName = $from ? $this->decodeMimeString($from->personal ?? '') : 'Unknown';
        $fromEmail = $from ? $from->mailbox . '@' . $from->host : 'unknown@example.com';

        // Parse recipient
        $to = $header->to[0] ?? null;
        $toName = $to ? $this->decodeMimeString($to->personal ?? '') : '';
        $toEmail = $to ? $to->mailbox . '@' . $to->host : '';

        // Get email body
        $body = $this->getEmailBody($mailbox, $emailId, $structure);

        // Get attachments
        $attachments = $this->getAttachments($mailbox, $emailId, $structure);

        // Create email record
        Email::create([
            'created_by' => 1, // Default admin user
            'type' => 'inbox',
            'from_name' => $fromName,
            'from_email' => $fromEmail,
            'to_name' => $toName,
            'to_email' => $toEmail,
            'cc' => $this->formatAddressList($header->cc ?? []),
            'subject' => $this->decodeMimeString($header->subject ?? 'No Subject'),
            'body' => $body['plain'] ?? strip_tags($body['html'] ?? ''),
            'body_html' => $body['html'] ?? null,
            'is_read' => false,
            'is_starred' => false,
            'is_important' => false,
            'sent_at' => isset($header->date) ? date('Y-m-d H:i:s', strtotime($header->date)) : now(),
            'attachments' => $attachments,
            'message_id' => $messageId,
            'in_reply_to' => $header->in_reply_to ?? null,
        ]);
    }

    /**
     * Get email body (plain text and HTML)
     */
    private function getEmailBody($mailbox, int $emailId, $structure): array
    {
        $body = ['plain' => '', 'html' => ''];

        if (!$structure->parts) {
            // Simple message
            $body['plain'] = $this->decodeBody(imap_body($mailbox, $emailId), $structure->encoding);
        } else {
            // Multipart message
            foreach ($structure->parts as $partNum => $part) {
                $partNum = $partNum + 1;
                $this->parsePart($mailbox, $emailId, $part, $partNum, $body);
            }
        }

        return $body;
    }

    /**
     * Recursively parse email parts
     */
    private function parsePart($mailbox, int $emailId, $part, string $partNum, array &$body): void
    {
        // Check if this part is text
        if ($part->type == 0) { // TYPETEXT
            $data = imap_fetchbody($mailbox, $emailId, $partNum);
            $decoded = $this->decodeBody($data, $part->encoding);

            if (strtolower($part->subtype) == 'plain') {
                $body['plain'] .= $decoded;
            } elseif (strtolower($part->subtype) == 'html') {
                $body['html'] .= $decoded;
            }
        }

        // Handle nested parts
        if (isset($part->parts)) {
            foreach ($part->parts as $subPartNum => $subPart) {
                $this->parsePart($mailbox, $emailId, $subPart, $partNum . '.' . ($subPartNum + 1), $body);
            }
        }
    }

    /**
     * Get attachments from email
     */
    private function getAttachments($mailbox, int $emailId, $structure): array
    {
        $attachments = [];

        if (!isset($structure->parts)) {
            return $attachments;
        }

        foreach ($structure->parts as $partNum => $part) {
            $partNum = $partNum + 1;

            // Check if this part is an attachment
            if (isset($part->disposition) && strtolower($part->disposition) == 'attachment') {
                $filename = $this->getFilenameFromPart($part);

                if ($filename) {
                    $data = imap_fetchbody($mailbox, $emailId, $partNum);
                    $decoded = $this->decodeBody($data, $part->encoding);

                    // Save to storage
                    $path = 'emails/attachments/' . uniqid() . '_' . $filename;
                    Storage::disk('local')->put($path, $decoded);

                    $attachments[] = [
                        'filename' => $filename,
                        'path' => $path,
                        'size' => strlen($decoded),
                        'mime_type' => $part->subtype ?? 'application/octet-stream',
                    ];
                }
            }
        }

        return $attachments;
    }

    /**
     * Get filename from MIME part
     */
    private function getFilenameFromPart($part): ?string
    {
        $filename = null;

        if (isset($part->dparameters)) {
            foreach ($part->dparameters as $param) {
                if (strtolower($param->attribute) == 'filename') {
                    $filename = $param->value;
                    break;
                }
            }
        }

        if (!$filename && isset($part->parameters)) {
            foreach ($part->parameters as $param) {
                if (strtolower($param->attribute) == 'name') {
                    $filename = $param->value;
                    break;
                }
            }
        }

        return $filename ? $this->decodeMimeString($filename) : null;
    }

    /**
     * Decode email body based on encoding
     */
    private function decodeBody(string $body, int $encoding): string
    {
        switch ($encoding) {
            case 3: // BASE64
                return base64_decode($body);
            case 4: // QUOTED-PRINTABLE
                return quoted_printable_decode($body);
            default:
                return $body;
        }
    }

    /**
     * Decode MIME encoded string
     */
    private function decodeMimeString(string $string): string
    {
        $decoded = imap_mime_header_decode($string);
        $result = '';

        foreach ($decoded as $element) {
            $result .= $element->text;
        }

        return $result;
    }

    /**
     * Format address list
     */
    private function formatAddressList(array $addresses): ?string
    {
        if (empty($addresses)) {
            return null;
        }

        $formatted = [];
        foreach ($addresses as $addr) {
            $email = $addr->mailbox . '@' . $addr->host;
            $name = $this->decodeMimeString($addr->personal ?? '');
            $formatted[] = $name ? "{$name} <{$email}>" : $email;
        }

        return implode(', ', $formatted);
    }

    /**
     * Build IMAP path
     */
    private function buildImapPath(string $folder): string
    {
        $host = $this->getImapHost();
        $port = $this->getImapPort();
        $encryption = $this->getImapEncryption();

        $flags = '/' . strtoupper($encryption);

        return "{{$host}:{$port}/imap{$flags}}{$folder}";
    }

    /**
     * Test IMAP connection
     */
    public function testConnection(): bool
    {
        return $this->connect();
    }

    /**
     * Connect to IMAP server
     */
    private function connect(): bool
    {
        if (!extension_loaded('imap')) {
            Log::error('IMAP extension not loaded');
            return false;
        }

        if (!$this->getImapHost() || !$this->getImapUsername() || !$this->getImapPassword()) {
            Log::error('IMAP settings incomplete');
            return false;
        }

        return true;
    }

    /**
     * Disconnect from IMAP server
     */
    private function disconnect(): void
    {
        // Connection is closed in fetch method
    }
}
