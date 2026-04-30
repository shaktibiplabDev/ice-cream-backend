<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = CompanySetting::getSettings();
        return view('admin.settings.index', compact('settings'));
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
