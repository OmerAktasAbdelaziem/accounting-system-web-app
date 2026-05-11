<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        // Get all settings
        $settings = Setting::all();
        
        // Get current values
        $currentSettings = [
            'app_name' => Setting::get('app_name', 'Aktas System'),
            'currency' => Setting::get('currency', 'AED'),
            'language' => Setting::get('language', 'en'),
            'timezone' => Setting::get('timezone', 'Asia/Dubai'),
            'financial_year_start' => Setting::get('financial_year_start', '01-01'),
            'tax_rate' => Setting::get('tax_rate', 5),
            'decimal_places' => Setting::get('decimal_places', 2),
            'date_format' => Setting::get('date_format', 'Y-m-d'),
            'enable_notifications' => Setting::get('enable_notifications', true),
            'enable_audit_logs' => Setting::get('enable_audit_logs', true),
        ];

        return view('settings.index', compact('currentSettings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'currency' => 'required|string|in:USD,AED,EGP,SAR,KWD,QAR,BHD,OMR,JOD',
            'language' => 'required|string|in:en,ar',
            'timezone' => 'required|string|timezone',
            'financial_year_start' => 'required|regex:/^\d{2}-\d{2}$/',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'decimal_places' => 'required|integer|min:0|max:4',
            'date_format' => 'required|string',
            'enable_notifications' => 'boolean',
            'enable_audit_logs' => 'boolean',
        ]);

        // Update each setting
        foreach ($validated as $key => $value) {
            if (is_bool($value)) {
                Setting::set($key, $value, 'boolean');
            } elseif (is_numeric($value)) {
                Setting::set($key, $value, 'integer');
            } else {
                Setting::set($key, $value, 'string');
            }
        }

        return back()->with('success', __('messages.settings_updated_successfully'));
    }
}
