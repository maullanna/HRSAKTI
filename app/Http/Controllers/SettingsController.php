<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }
    

    public function update(Request $request)
    {
        // Debug: Log all request data
        \Log::info('Settings update request received:', $request->all());
        
        $request->validate([
            'ams_logo_type' => 'required|in:predefined,upload',
            'ams_logo' => 'required_if:ams_logo_type,predefined|string',
            'ams_logo_upload' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'system_name' => 'nullable|string|max:30',
            'system_name_visible' => 'nullable|in:0,1',
            'footer_text' => 'required|string|max:1000',
        ]);

        try {
            $settings = [];
            
            // Debug logging
            \Log::info('Settings update request:', $request->all());
            
            // AMS Logo settings
            
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
                } else {
                    // Keep existing logo if no new file uploaded
                    $settings['ams_logo_type'] = 'upload';
                }
            }
            
            // System name settings - Always save even if empty
            $settings['system_name'] = $request->input('system_name', 'HRSAKTI');
            $settings['system_name_visible'] = $request->input('system_name_visible', '1');
            
            // Footer settings
            $settings['footer_text'] = $request->footer_text;
            
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
