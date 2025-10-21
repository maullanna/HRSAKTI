<?php

if (!function_exists('getSetting')) {
    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function getSetting($key, $default = null)
    {
        // Load settings from file directly
        $settingsPath = storage_path('app/settings.json');
        
        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true);
        } else {
            $settings = [];
        }
        
        // Default values if not set
        $defaults = [
            'ams_logo' => 'assets/images/logo.png',
            'ams_logo_type' => 'predefined',
            'footer_text' => 'Attendance Management System',
            'footer_show_year' => 1,
            'footer_show_author' => 1,
        ];

        return $settings[$key] ?? $defaults[$key] ?? $default;
    }
}

if (!function_exists('getFooterText')) {
    /**
     * Get formatted footer text
     *
     * @return string
     */
    function getFooterText()
    {
        $footerText = getSetting('footer_text', 'Attendance Management System');
        $currentYear = date('Y');
        
        // Always add year at the beginning, regardless of settings
        $finalText = '© ' . $currentYear . ' ' . $footerText;
        
        // Remove any existing year patterns from the text to avoid duplication
        $finalText = preg_replace('/©\s*\d{4}\s*/', '', $finalText);
        $finalText = '© ' . $currentYear . ' ' . trim($finalText);
        
        return $finalText;
    }
}