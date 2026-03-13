<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\ActivityType;
use App\Models\CourseStudent;
use App\Models\Profile;
use App\Support\Api\ApiResponse;
use App\Support\Transformers\CoreTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function staff(Request $request)
    {
        $studentCount = Profile::query()->where('type', 'student')->where('is_active', true)->count();
        $teacherCount = Profile::query()->where('type', 'teacher')->where('is_active', true)->count();

        $classDistribution = Profile::query()->getModel()->newQuery();
        $classDistribution = \App\Models\Course::query()
            ->where('is_active', true)
            ->withCount(['studentEnrollments as student_count' => fn ($query) => $query->where('is_active', true)])
            ->get()
            ->map(fn ($course) => [
                'id' => $course->id,
                'name' => $course->name,
                'student_count' => $course->student_count,
            ])
            ->all();

        $recentStudents = Profile::query()
            ->where('type', 'student')
            ->where('is_active', true)
            ->latest()
            ->limit(5)
            ->get();

        $today = Carbon::today();
        $fiveDaysAgo = $today->copy()->subDays(5);
        $twelveDaysAgo = $today->copy()->subDays(12);

        $attendanceTypeIds = ActivityType::query()
            ->where('type', 'boolean')
            ->where('name', 'like', '%attendance%')
            ->pluck('id');

        $attendanceActivityIds = Activity::query()
            ->whereIn('activity_type_id', $attendanceTypeIds)
            ->pluck('id');

        $currentAbsences = ActivityLog::query()
            ->whereIn('activity_id', $attendanceActivityIds)
            ->where('score', 1)
            ->whereDate('created_at', '>=', $fiveDaysAgo)
            ->whereDate('created_at', '<=', $today)
            ->count();

        $priorAbsences = ActivityLog::query()
            ->whereIn('activity_id', $attendanceActivityIds)
            ->where('score', 1)
            ->whereDate('created_at', '>=', $twelveDaysAgo)
            ->whereDate('created_at', '<', $fiveDaysAgo)
            ->count();

        $absenceTrend = collect(range(0, 4))->map(function (int $index) use ($today, $attendanceActivityIds) {
            $date = $today->copy()->subDays(4 - $index);

            return [
                'date' => $date->toDateString(),
                'count' => ActivityLog::query()
                    ->whereIn('activity_id', $attendanceActivityIds)
                    ->where('score', 1)
                    ->whereDate('created_at', $date)
                    ->count(),
            ];
        })->all();

        return ApiResponse::ok([
            'student_count' => $studentCount,
            'teacher_count' => $teacherCount,
            'class_distribution' => $classDistribution,
            'recent_students' => $recentStudents->map(fn (Profile $student) => CoreTransformer::profile($student))->all(),
            'absence_trend' => $absenceTrend,
            'current_absences' => $currentAbsences,
            'prior_absences' => $priorAbsences,
        ]);
    }

    public function student(Request $request)
    {
        $profile = $request->user()->profile;
        $classes = CourseStudent::query()
            ->where('student_id', $profile->id)
            ->where('is_active', true)
            ->with('course.grade', 'course.subject')
            ->get()
            ->map(fn (CourseStudent $enrollment) => [
                'id' => $enrollment->course->id,
                'name' => $enrollment->course->name,
                'grade' => $enrollment->course->grade?->name,
                'subject' => $enrollment->course->subject?->name,
            ])
            ->all();

        return ApiResponse::ok([
            'profile' => CoreTransformer::profile($profile),
            'classes' => $classes,
        ]);
    }
}
