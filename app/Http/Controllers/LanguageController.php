<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch language
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLanguage(Request $request, $locale)
    {
        // Validate locale
        $allowedLocales = ['en', 'id'];
        
        if (!in_array($locale, $allowedLocales)) {
            $locale = 'en'; // Default to English if invalid
        }

        // Set locale in session
        Session::put('locale', $locale);
        
        // Set locale for current request
        App::setLocale($locale);

        // Redirect back to previous page
        return redirect()->back();
    }
}

