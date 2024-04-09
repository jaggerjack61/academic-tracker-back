<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseTeacher;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClassController extends Controller
{
    //
    public function showClasses()
    {
        $grades = Grade::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = Teacher::where('is_active', true)->get();
        $classes = Course::paginate(30);
        return view('pages.classes.index', compact('grades', 'subjects', 'teachers','classes'));
    }

    public function create(Request $request)
    {
        // Validate the request data


        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'grade_id' => 'required|integer',
                'subject_id' => 'required|integer',
                'teacher_id' => 'nullable|integer',
                'description' => 'nullable|string',
            ]);
            $course = Course::create($validatedData);
            $course->save();
            if($validatedData['teacher_id'] != null) {
                $courseTeacher = CourseTeacher::where('course_id',$course->id)
                    ->where('is_active', true)
                    ->first();
                if($courseTeacher == null) {
                    $courseTeacher = new CourseTeacher();
                    $courseTeacher->course_id = $course->id;
                    $courseTeacher->teacher_id = $validatedData['teacher_id'];
                    $courseTeacher->is_active = true;
                    $courseTeacher->save();
                }
                else{
                    return back()->with('error', 'Class has already been assigned.');
                }
            }

            return back()->with('success', 'Class has been created');
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to create class');
        }
    }
}

