<?php

namespace App\Services;

class EnvService
{
    protected string $envPath;

    public function __construct()
    {
        $this->envPath = base_path('.env');
    }

    public function get(string $key, string $default = ''): string
    {
        return env($key, $default);
    }

    public function set(array $data): bool
    {
        if (!file_exists($this->envPath)) {
            return false;
        }

        $content = file_get_contents($this->envPath);

        foreach ($data as $key => $value) {
            // Escape special characters in value
            $value = str_replace('"', '\\"', $value);
            
            // Check if key exists
            $pattern = '/^' . preg_quote($key, '/') . '=.*/m';
            
            if (preg_match($pattern, $content)) {
                // Update existing key
                $content = preg_replace($pattern, $key . '="' . $value . '"', $content);
            } else {
                // Add new key at the end
                $content .= "\n" . $key . '="' . $value . '"';
            }
        }

        return file_put_contents($this->envPath, $content) !== false;
    }

    public function getEmailSettings(): array
    {
        return [
            // IMAP Settings
            'imap_host' => $this->get('IMAP_HOST', ''),
            'imap_port' => $this->get('IMAP_PORT', '993'),
            'imap_username' => $this->get('IMAP_USERNAME', ''),
            'imap_password' => $this->get('IMAP_PASSWORD', ''),
            'imap_encryption' => $this->get('IMAP_ENCRYPTION', 'ssl'),
            'imap_folder' => $this->get('IMAP_FOLDER', 'INBOX'),
            
            // SMTP Settings
            'mail_mailer' => $this->get('MAIL_MAILER', 'smtp'),
            'mail_host' => $this->get('MAIL_HOST', ''),
            'mail_port' => $this->get('MAIL_PORT', '587'),
            'mail_username' => $this->get('MAIL_USERNAME', ''),
            'mail_password' => $this->get('MAIL_PASSWORD', ''),
            'mail_encryption' => $this->get('MAIL_ENCRYPTION', 'tls'),
            'mail_from_address' => $this->get('MAIL_FROM_ADDRESS', ''),
            'mail_from_name' => $this->get('MAIL_FROM_NAME', config('app.name')),
        ];
    }

    public function setEmailSettings(array $data): bool
    {
        $envData = [
            'IMAP_HOST' => $data['imap_host'] ?? '',
            'IMAP_PORT' => $data['imap_port'] ?? '993',
            'IMAP_USERNAME' => $data['imap_username'] ?? '',
            'IMAP_PASSWORD' => $data['imap_password'] ?? '',
            'IMAP_ENCRYPTION' => $data['imap_encryption'] ?? 'ssl',
            'IMAP_FOLDER' => $data['imap_folder'] ?? 'INBOX',
            'MAIL_MAILER' => $data['mail_mailer'] ?? 'smtp',
            'MAIL_HOST' => $data['mail_host'] ?? '',
            'MAIL_PORT' => $data['mail_port'] ?? '587',
            'MAIL_USERNAME' => $data['mail_username'] ?? '',
            'MAIL_PASSWORD' => $data['mail_password'] ?? '',
            'MAIL_ENCRYPTION' => $data['mail_encryption'] ?? 'tls',
            'MAIL_FROM_ADDRESS' => $data['mail_from_address'] ?? '',
            'MAIL_FROM_NAME' => $data['mail_from_name'] ?? config('app.name'),
        ];

        return $this->set($envData);
    }
}
