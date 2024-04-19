<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function showDashboard()
    {
        $students = Student::where('is_active', true)->orderBy('id', 'desc')->take(8)->get();
        $studentCount = Student::where('is_active', true)->count();
        $teachers = Teacher::where('is_active', true)->get();
        $classes = Course::where('is_active', true)->get();
        return view('pages.dashboard', compact('students','teachers', 'studentCount', 'classes'));
    }
}
