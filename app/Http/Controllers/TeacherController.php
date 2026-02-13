<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Profile;

class TeacherController extends Controller
{
    public function show()
    {

        return view('pages.teachers.index');
    }

    public function view(Profile $teacher)
    {
        $classes = Course::where('is_active', true)->get();

        return view('pages.teachers.view', compact('classes', 'teacher'));
    }
}
