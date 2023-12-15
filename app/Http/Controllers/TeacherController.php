<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function showTeachers()
    {
        return view('pages.teachers.index');
    }

    public function view()
    {
        return view('pages.teachers.view');
    }
}
