<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function show()
    {

        return view('pages.teachers.index');
    }

    public function view(Teacher $teacher)
    {
        $classes = Course::where('is_active', true)->get();
        return view('pages.teachers.view', compact('classes', 'teacher'));
    }
}
