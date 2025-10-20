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
            'ams_name' => 'AMS',
            'footer_text' => '© 2025 Attendance Management System - Crafted with ❤️ by Ali Aqa Atayee.',
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
        $footerText = getSetting('footer_text', '© 2025 Attendance Management System - Crafted with ❤️ by Ali Aqa Atayee.');
        
        if (getSetting('footer_show_year', 1)) {
            $currentYear = date('Y');
            $footerText = str_replace('2025', $currentYear, $footerText);
        }
        
        if (!getSetting('footer_show_author', 1)) {
            $footerText = preg_replace('/ - Crafted with.*$/', '', $footerText);
        }
        
        return $footerText;
    }
}