<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\CourseStudent;
use App\Support\Api\ApiResponse;
use App\Support\Transformers\CoreTransformer;
use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    public function courseAssignments(Request $request, int $course_id)
    {
        $profile = $request->user()->profile;
        $enrolled = CourseStudent::query()
            ->where('course_id', $course_id)
            ->where('student_id', $profile->id)
            ->where('is_active', true)
            ->exists();

        if (! $enrolled) {
            return ApiResponse::forbidden('Not enrolled in this class');
        }

        $activities = Activity::query()->where('course_id', $course_id)->with('activityType', 'teacher', 'course', 'term')->get();
        $logs = ActivityLog::query()
            ->where('student_id', $profile->id)
            ->where('is_active', true)
            ->whereHas('activity', fn ($query) => $query->where('course_id', $course_id))
            ->with('activity.activityType', 'activity.teacher', 'activity.course', 'activity.term')
            ->get()
            ->keyBy('activity_id');

        $groups = [];
        foreach ($activities as $activity) {
            $type = $activity->activityType;
            $key = $type->id;
            if (! isset($groups[$key])) {
                $groups[$key] = [
                    'activity_type' => CoreTransformer::activityType($type),
                    'items' => [],
                ];
            }

            $log = $logs->get($activity->id);
            $item = [
                'activity' => CoreTransformer::activity($activity),
            ];

            if ($type->type === 'value') {
                $item['mark'] = $log?->score;
                $item['total'] = $activity->total;
                $item['percentage'] = ($log && $log->score !== null && $activity->total)
                    ? round(($log->score / $activity->total) * 100, 1)
                    : null;
                $item['file'] = $log?->file ?? '';
            } elseif ($type->type === 'boolean') {
                if ($log && $log->score === 2.0) {
                    $item['result'] = true;
                    $item['label'] = $type->true_value ?: 'Present';
                } elseif ($log && $log->score === 1.0) {
                    $item['result'] = false;
                    $item['label'] = $type->false_value ?: 'Absent';
                } else {
                    $item['result'] = null;
                    $item['label'] = '';
                }
                $item['file'] = $log?->file ?? '';
            } else {
                $item['file'] = $log?->file ?? '';
            }

            $groups[$key]['items'][] = $item;
        }

        return ApiResponse::ok(['groups' => array_values($groups)]);
    }

    public function allAssignments(Request $request)
    {
        $profile = $request->user()->profile;
        $courseFilter = $request->query('course');
        $enrolledCourses = CourseStudent::query()
            ->where('student_id', $profile->id)
            ->where('is_active', true)
            ->pluck('course_id');

        $logs = ActivityLog::query()
            ->where('student_id', $profile->id)
            ->where('is_active', true)
            ->whereHas('activity', function ($query) use ($enrolledCourses, $courseFilter): void {
                $query->whereIn('course_id', $enrolledCourses);
                if ($courseFilter) {
                    $query->where('course_id', $courseFilter);
                }
            })
            ->with('activity.activityType', 'activity.course', 'activity.term')
            ->get();

        $assignments = $logs->map(function (ActivityLog $log) {
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
                $entry['file'] = $log->file ?: '';
            }

            return $entry;
        })->all();

        return ApiResponse::ok(['assignments' => $assignments]);
    }
}
