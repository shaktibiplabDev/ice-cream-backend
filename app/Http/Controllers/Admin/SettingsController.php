<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Services\EnvService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    // Company Settings
    public function company()
    {
        $settings = CompanySetting::getSettings();
        return view('admin.settings.company', compact('settings'));
    }

    // Tax & GST Settings
    public function tax()
    {
        $settings = CompanySetting::getSettings();
        return view('admin.settings.tax', compact('settings'));
    }

    // Invoice Settings
    public function invoice()
    {
        $settings = CompanySetting::getSettings();
        return view('admin.settings.invoice', compact('settings'));
    }

    // Bank Settings
    public function bank()
    {
        $settings = CompanySetting::getSettings();
        return view('admin.settings.bank', compact('settings'));
    }

    // Email Settings
    public function email()
    {
        $settings = CompanySetting::getSettings();
        return view('admin.settings.email', compact('settings'));
    }

    // Legacy redirect
    public function index()
    {
        return redirect()->route('admin.settings.company');
    }

    public function update(Request $request)
    {
        $settings = CompanySetting::getSettings();

        $validated = $request->validate([
            // Company Info
            'company_name' => 'required|string|max:255',
            'company_legal_name' => 'nullable|string|max:255',
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
            
            // Tax/GST Info
            'gst_number' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|max:20',
            'fssai_number' => 'nullable|string|max:20',
            
            // GST Settings
            'gst_type' => 'required|in:b2b,b2c,none',
            'gst_percentage' => 'required|numeric|min:0|max:100',
            'cgst_percentage' => 'required|numeric|min:0|max:100',
            'sgst_percentage' => 'required|numeric|min:0|max:100',
            'igst_percentage' => 'required|numeric|min:0|max:100',
            
            // Invoice Settings
            'invoice_prefix' => 'required|string|max:10',
            'invoice_terms' => 'nullable|string|max:50',
            'invoice_footer_text' => 'nullable|string',
            
            // Currency
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:5',
            
            // Bank Details
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc_code' => 'nullable|string|max:20',
            'bank_branch' => 'nullable|string|max:100',
            
            // Terms
            'terms_and_conditions' => 'nullable|string',

            // Email Configuration (IMAP for receiving)
            'email_fetching_enabled' => 'boolean',
            'imap_host' => 'nullable|string|max:255',
            'imap_port' => 'nullable|integer',
            'imap_username' => 'nullable|string|max:255',
            'imap_password' => 'nullable|string|max:255',
            'imap_encryption' => 'nullable|string|max:10',
            'imap_folder' => 'nullable|string|max:50',

            // Email Configuration (SMTP for sending)
            'mail_mailer' => 'nullable|string|max:20',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|max:10',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
            
            // Backup settings
            'backup_days' => 'nullable|integer|min:1|max:365',
        ]);

        $validated['email_fetching_enabled'] = $request->boolean('email_fetching_enabled', false);

        // Sync email settings with .env file
        $envService = new EnvService();
        $envService->setEmailSettings([
            'imap_host' => $validated['imap_host'] ?? '',
            'imap_port' => $validated['imap_port'] ?? '993',
            'imap_username' => $validated['imap_username'] ?? '',
            'imap_password' => $validated['imap_password'] ?? '',
            'imap_encryption' => $validated['imap_encryption'] ?? 'ssl',
            'imap_folder' => $validated['imap_folder'] ?? 'INBOX',
            'mail_mailer' => $validated['mail_mailer'] ?? 'smtp',
            'mail_host' => $validated['mail_host'] ?? '',
            'mail_port' => $validated['mail_port'] ?? '587',
            'mail_username' => $validated['mail_username'] ?? '',
            'mail_password' => $validated['mail_password'] ?? '',
            'mail_encryption' => $validated['mail_encryption'] ?? 'tls',
            'mail_from_address' => $validated['mail_from_address'] ?? '',
            'mail_from_name' => $validated['mail_from_name'] ?? config('app.name'),
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($settings->logo_path && Storage::exists($settings->logo_path)) {
                Storage::delete($settings->logo_path);
            }
            
            $path = $request->file('logo')->store('company', 'public');
            $validated['logo_path'] = $path;
        }

        // Remove logo from validated if not uploaded
        unset($validated['logo']);

        $settings->update($validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Company settings updated successfully');
    }
}
