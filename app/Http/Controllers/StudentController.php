<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function showStudents()
    {
        return view('pages.students.index');
    }

    public function view()
    {
        return view('pages.students.view');
    }

    public function viewActivities()
    {
        return view('pages.students.activities');
    }
}
