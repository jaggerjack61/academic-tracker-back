<?php

namespace App\Http\Controllers;

class SettingsController extends Controller
{
    public function showTerms()
    {
        return view('pages.settings.terms');
    }
}
