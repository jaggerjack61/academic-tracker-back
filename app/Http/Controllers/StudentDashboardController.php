<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ActivityType;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    /**
     * Show the student dashboard with enrolled classes.
     */
    public function showDashboard()
    {
        $user = Auth::user();
        $student = $user->student()->first();

        if (! $student) {
            return response('Student profile not found.', 404);
        }

        // Get enrolled classes
        $courses = $student->courses()->with(['course.grade', 'course.subject'])->get();

        return view('pages.student.student-dashboard', compact('student', 'courses'));
    }

    /**
     * Show assignments and marks for a specific course.
     */
    public function showAssignments($courseId)
    {
        $user = Auth::user();
        $student = $user->student()->first();

        if (! $student) {
            return response('Student profile not found.', 404);
        }

        // Verify student is enrolled in this course
        $enrollment = $student->courses()->where('course_id', $courseId)->first();
        if (! $enrollment) {
            return response('You are not enrolled in this class.', 403);
        }

        $course = Course::with(['grade', 'subject'])->findOrFail($courseId);

        // Get all activity logs for this student and course
        $logs = ActivityLog::where('student_id', $student->id)
            ->whereHas('activity', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->with(['activity.type', 'activity.course'])
            ->get();

        // Get all activity types for tabs
        $activityTypes = ActivityType::where('is_active', true)->get();
        $first = $activityTypes->first()?->id;

        return view('pages.student.student-assignments', compact('student', 'course', 'logs', 'activityTypes', 'first'));
    }
}
