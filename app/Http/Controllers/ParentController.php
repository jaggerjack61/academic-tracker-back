<?php

namespace App\Http\Controllers;

use App\Models\StudentParent;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function show()
    {
        $parents = StudentParent::where('is_active', true)->get();
        return view('pages.parents.index', compact('parents'));
    }
}
