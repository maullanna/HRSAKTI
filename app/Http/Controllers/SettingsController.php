<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings');
    }
    
    public function test()
    {
        // Test method to verify settings are working
        $settings = [
            'ams_logo' => 'assets/images/logo.png',
            'ams_logo_type' => 'predefined',
            'ams_name' => 'Test Company Name - ' . now()->format('H:i:s'),
            'footer_text' => 'Test Footer from Controller - ' . now(),
            'footer_show_year' => 1,
            'footer_show_author' => 1,
        ];
        
        $this->saveSettings($settings);
        
        return response()->json([
            'message' => 'Settings updated successfully',
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        // Debug: Log all request data
        \Log::info('Settings update request received:', $request->all());
        
        $request->validate([
            'ams_logo_type' => 'required|in:predefined,upload',
            'ams_logo' => 'required_if:ams_logo_type,predefined|string',
            'ams_logo_upload' => 'required_if:ams_logo_type,upload|image|mimes:png,jpg,jpeg,svg|max:2048',
            'ams_name' => 'required|string|max:100',
            'footer_text' => 'required|string|max:1000',
            'footer_show_year' => 'nullable|boolean',
            'footer_show_author' => 'nullable|boolean',
        ]);

        try {
            $settings = [];
            
            // Debug logging
            \Log::info('Settings update request:', $request->all());
            \Log::info('AMS Name from request:', ['ams_name' => $request->ams_name]);
            
            // AMS Logo settings
            $settings['ams_name'] = $request->ams_name;
            
            if ($request->ams_logo_type === 'predefined') {
                $settings['ams_logo'] = $request->ams_logo;
                $settings['ams_logo_type'] = 'predefined';
            } else {
                // Handle file upload
                if ($request->hasFile('ams_logo_upload')) {
                    $file = $request->file('ams_logo_upload');
                    $filename = 'ams_logo_' . time() . '.' . $file->getClientOriginalExtension();
                    
                    // Store in public/storage/logos
                    $path = $file->storeAs('public/logos', $filename);
                    $settings['ams_logo'] = 'storage/logos/' . $filename;
                    $settings['ams_logo_type'] = 'upload';
                }
            }
            
            // Footer settings
            $settings['footer_text'] = $request->footer_text;
            $settings['footer_show_year'] = (int) $request->footer_show_year;
            $settings['footer_show_author'] = (int) $request->footer_show_author;
            
            // Debug logging
            \Log::info('Settings to save:', $settings);
            
            // Save settings to file
            $this->saveSettings($settings);
            
            // Debug: Verify settings were saved
            \Log::info('Settings saved successfully:', $settings);
            
            return redirect()->route('settings')->with('success', 'Settings updated successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Settings update failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('settings')
                ->with('error', 'Failed to update settings: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    private function saveSettings($settings)
    {
        // Create settings directory if it doesn't exist
        $settingsPath = storage_path('app/settings.json');
        
        // Load existing settings
        $existingSettings = [];
        if (File::exists($settingsPath)) {
            $existingSettings = json_decode(File::get($settingsPath), true) ?: [];
        }
        
        // Merge with new settings
        $allSettings = array_merge($existingSettings, $settings);
        
        // Save to file
        File::put($settingsPath, json_encode($allSettings, JSON_PRETTY_PRINT));
    }
    
    public static function getSettings()
    {
        $settingsPath = storage_path('app/settings.json');
        
        if (File::exists($settingsPath)) {
            return json_decode(File::get($settingsPath), true) ?: [];
        }
        
        return [];
    }
}
