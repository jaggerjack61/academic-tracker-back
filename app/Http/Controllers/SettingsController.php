<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function showTerms()
    {
        return view('pages.settings.terms');
    }
}
