<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\Profile;
use App\Support\Api\ApiResponse;
use App\Support\Api\ManualPaginator;
use App\Support\Collab\ClassGroupSyncService;
use App\Support\Transformers\CoreTransformer;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->query('search', '');

        $query = Profile::query()->where('type', 'student')->with('user');
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('id_number', 'like', "%{$search}%")
                    ->orWhere('dob', 'like', "%{$search}%")
                    ->orWhere('sex', 'like', "%{$search}%");
            });
        }

        $query->latest();
        [$students, $total, $page, $pageSize] = ManualPaginator::fromQuery($query, $request, 10);

        return ApiResponse::paginated(
            $students->map(fn (Profile $student) => CoreTransformer::profile($student))->all(),
            $total,
            $page,
            $pageSize,
        );
    }

    public function show(int $pk)
    {
        $student = Profile::query()->where('id', $pk)->where('type', 'student')->with('user')->first();
        if (! $student) {
            return ApiResponse::notFound('Student not found');
        }

        $classes = CourseStudent::query()
            ->where('student_id', $student->id)
            ->where('is_active', true)
            ->with('course.grade', 'course.subject')
            ->get()
            ->map(fn (CourseStudent $enrollment) => [
                'enrollment_id' => $enrollment->id,
                'course_id' => $enrollment->course_id,
                'course_name' => $enrollment->course?->name,
                'grade' => $enrollment->course?->grade?->name,
                'subject' => $enrollment->course?->subject?->name,
                'is_active' => (bool) $enrollment->is_active,
            ])
            ->all();

        return ApiResponse::ok([
            'student' => CoreTransformer::profile($student),
            'classes' => $classes,
        ]);
    }

    public function enroll(Request $request, int $pk)
    {
        $student = Profile::query()->where('id', $pk)->where('type', 'student')->first();
        if (! $student) {
            return ApiResponse::notFound('Student not found');
        }

        foreach ((array) $request->input('course_ids', []) as $courseId) {
            $course = Course::query()->find($courseId);
            if (! $course) {
                continue;
            }

            $enrollment = CourseStudent::query()->firstOrCreate(
                ['course_id' => $course->id, 'student_id' => $student->id],
                ['is_active' => true]
            );

            if (! $enrollment->is_active) {
                $enrollment->update(['is_active' => true]);
            }

            ClassGroupSyncService::sync($course);
        }

        return ApiResponse::message('Student enrolled successfully');
    }

    public function unenroll(Request $request, int $pk)
    {
        $enrollment = CourseStudent::query()
            ->where('student_id', $pk)
            ->where('course_id', $request->input('course_id'))
            ->where('is_active', true)
            ->with('course')
            ->first();

        if (! $enrollment) {
            return ApiResponse::notFound('Enrollment not found');
        }

        $enrollment->update(['is_active' => false]);
        ClassGroupSyncService::sync($enrollment->course);

        return ApiResponse::message('Student un-enrolled');
    }

    public function toggleStatus(int $pk)
    {
        $student = Profile::query()->where('id', $pk)->where('type', 'student')->first();
        if (! $student) {
            return ApiResponse::notFound('Student not found');
        }

        $student->update(['is_active' => ! $student->is_active]);

        return ApiResponse::message('Student status updated', ['is_active' => $student->is_active]);
    }

    public function courseHistory(int $student_id, int $course_id)
    {
        $activities = Activity::query()->where('course_id', $course_id)->with('activityType', 'teacher', 'course', 'term')->get();
        $logs = ActivityLog::query()
            ->where('student_id', $student_id)
            ->whereHas('activity', fn ($query) => $query->where('course_id', $course_id))
            ->where('is_active', true)
            ->with('activity.activityType', 'activity.teacher', 'activity.course', 'activity.term')
            ->get()
            ->keyBy('activity_id');

        $groups = [];
        foreach ($activities as $activity) {
            $key = $activity->activityType->id;
            if (! isset($groups[$key])) {
                $groups[$key] = [
                    'activity_type' => CoreTransformer::activityType($activity->activityType),
                    'items' => [],
                ];
            }

            $groups[$key]['items'][] = [
                'activity' => CoreTransformer::activity($activity),
                'log' => CoreTransformer::activityLog($logs->get($activity->id)),
            ];
        }

        return ApiResponse::ok(['groups' => array_values($groups)]);
    }

    public function allHistory(Request $request)
    {
        $studentId = $request->route('student_id');
        $student = Profile::query()->where('id', $studentId)->where('type', 'student')->first();
        if (! $student) {
            return ApiResponse::notFound('Student not found');
        }

        $history = ActivityLog::query()
            ->where('student_id', $student->id)
            ->where('is_active', true)
            ->with('activity.activityType', 'activity.course', 'activity.term')
            ->get()
            ->map(function (ActivityLog $log) {
                $activity = $log->activity;
                $type = $activity?->activityType;
                $entry = [
                    'course_name' => $activity?->course?->name ?? '',
                    'activity_name' => $activity?->name ?? '',
                    'term_name' => $activity?->term?->name ?? '',
                    'activity_type' => $type?->type ?? 'value',
                ];

                if ($type?->type === 'value') {
                    $entry['value'] = $log->score;
                    $entry['total'] = $activity?->total;
                } elseif ($type?->type === 'boolean') {
                    $entry['bool_value'] = $log->score === 2.0;
                    $entry['bool_label'] = (($log->score === 2.0) ? $type->true_value : $type->false_value) ?: '';
                } else {
                    $entry['note'] = $activity?->note ?? '';
                }

                return $entry;
            })
            ->all();

        return ApiResponse::ok([
            'student_name' => $student->full_name,
            'history' => $history,
        ]);
    }
}
