<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ActivityType;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function showStudents()
    {
        return view('pages.students.index');
    }

    public function view(Student $student)
    {
        $classes = Course::where('is_active', true)->get();

        return view('pages.students.view', compact('student', 'classes'));
    }

    public function enroll(Request $request)
    {
        try {
            foreach ($request->class as $course_id) {
                $enrolled = $this->validateEnrollment($request->student_id, $course_id);
                if ($enrolled) {
                    if (! $enrolled->is_active) {
                        $enrolled->is_active = true;
                        $enrolled->save();
                    }
                } else {
                    $student = new CourseStudent();
                    $student->student_id = $request->student_id;
                    $student->course_id = $course_id;
                    $student->save();
                }

            }

            return redirect()->back()->with('success', 'Enrolled Successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function unenroll($student, $class)
    {
        try {
            //            dd($student, $class);
            $course = CourseStudent::where('student_id', $student)->where('course_id', $class)->first();
            $course->is_active = false;
            $course->save();

            return redirect()->back()->with('success', 'You have been un-enrolled from '.$course->course->name);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

    }

    public function validateEnrollment($student_id, $class_id)
    {
        $enrolled = CourseStudent::where('student_id', $student_id)->where('course_id', $class_id)->first();

        return $enrolled;
    }

    public function viewActivities(Student $student, Course $course)
    {
        $logs = ActivityLog::where('student_id', $student->id)
            ->whereHas('activity', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->get();
        //        dd($logs);
        $activityTypes = ActivityType::where('is_active', true)->get();
        $first = ActivityType::where('is_active', true)->first()->id;

        return view('pages.students.activities', compact('student', 'course', 'activityTypes', 'first', 'logs'));

        return view('pages.students.activities');
    }
}
