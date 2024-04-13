<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\CourseTeacher;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClassController extends Controller
{
    public $studentController;
    public function __construct()
    {
        $this->studentController = new StudentController();
    }
    //
    public function showClasses()
    {
        $grades = Grade::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = Teacher::where('is_active', true)->get();
        $classes = Course::paginate(30);
        return view('pages.classes.index', compact('grades', 'subjects', 'teachers', 'classes'));
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
            if ($validatedData['teacher_id'] != null) {
                $courseTeacher = CourseTeacher::where('course_id', $course->id)
                    ->where('is_active', true)
                    ->first();
                if ($courseTeacher == null) {
                    $courseTeacher = new CourseTeacher();
                    $courseTeacher->course_id = $course->id;
                    $courseTeacher->teacher_id = $validatedData['teacher_id'];
                    $courseTeacher->is_active = true;
                    $courseTeacher->save();
                } else {
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

    public function edit(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required|integer',
                'name' => 'required|string',
                'grade_id' => 'required|integer',
                'subject_id' => 'required|integer',
                'teacher_id' => 'nullable|integer',
                'description' => 'nullable|string',
            ]);
            $course = Course::find($validatedData['id']);
            $course->update($validatedData);
            $courseTeacher = CourseTeacher::where('course_id', $course->id)->where('is_active', true)->first();
            if ($courseTeacher == null && $validatedData['teacher_id'] != null) {
                $courseTeacher = new CourseTeacher();
                $courseTeacher->course_id = $course->id;
                $courseTeacher->teacher_id = $validatedData['teacher_id'];
                $courseTeacher->is_active = true;
                $courseTeacher->save();
            } else if ($courseTeacher != null && $validatedData['teacher_id'] != null) {
                if ($courseTeacher->teacher_id != $validatedData['teacher_id']) {
                    $courseTeacher->is_active = false;
                    $courseTeacher->save();
                    $courseTeacher = new CourseTeacher();
                    $courseTeacher->course_id = $course->id;
                    $courseTeacher->teacher_id = $validatedData['teacher_id'];
                    $courseTeacher->is_active = true;
                    $courseTeacher->save();
                }
            }
            return back()->with('success', 'Class has been updated');
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update class');
        }
    }

    public function toggle(Course $course)
    {
        $course->is_active = !$course->is_active;
        $course->save();
        return back()->with('success', 'Class status has been updated');
    }

    public function view(Course $course)
    {
        $students = Student::where('is_active', true)->get();
        $teachers = Teacher::where('is_active', true)->get();
        return view('pages.classes.view', [
            'class' => $course,
            'students' => $students,
            'teachers' => $teachers
        ]);
    }

    public function enroll(Request $request)
    {
//        dd($request);
        $class = Course::find($request->course_id);
        foreach ($request->students as $student) {
            $enrolled = $this->studentController->validateEnrollment($student, $class->id);
            if($enrolled) {
                if(!$enrolled->is_active){
                    $enrolled->is_active = true;
                    $enrolled->save();
                }
            } else {
                $studentInstance = new CourseStudent();
                $studentInstance->student_id = $student;
                $studentInstance->course_id = $class->id;
                $studentInstance->save();
            }
        }
        return back()->with('success', 'Students have been enrolled');
    }
}

