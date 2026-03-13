<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\CourseStudent;
use App\Models\Term;
use App\Support\Api\ApiResponse;
use App\Support\Transformers\CoreTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function courseActivities(int $course_id)
    {
        $activities = Activity::query()
            ->where('course_id', $course_id)
            ->with('activityType', 'teacher', 'course', 'term')
            ->latest()
            ->get();

        $groups = [];
        foreach ($activities as $activity) {
            $key = $activity->activityType->id;
            if (! isset($groups[$key])) {
                $groups[$key] = [
                    'activity_type' => CoreTransformer::activityType($activity->activityType),
                    'activities' => [],
                ];
            }

            $groups[$key]['activities'][] = CoreTransformer::activity($activity);
        }

        return ApiResponse::ok([
            'groups' => array_values($groups),
            'all' => $activities->map(fn (Activity $activity) => CoreTransformer::activity($activity))->all(),
        ]);
    }

    public function store(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $activeTerm = Term::query()
            ->where('is_active', true)
            ->where('start', '<=', $today)
            ->where('end', '>=', $today)
            ->first();

        if (! $activeTerm) {
            return ApiResponse::error('No active term covers the current date. Cannot create activity.');
        }

        $activity = Activity::query()->create([
            'name' => (string) $request->input('name', ''),
            'teacher_id' => $request->input('teacher_id', $request->user()->profile->id),
            'activity_type_id' => $request->input('activity_type_id'),
            'course_id' => $request->input('course_id'),
            'term_id' => $activeTerm->id,
            'note' => (string) $request->input('note', ''),
            'total' => $request->input('total'),
            'due_date' => (string) $request->input('due_date', ''),
            'file' => (string) $request->input('file', ''),
            'is_active' => true,
        ]);

        $logs = CourseStudent::query()
            ->where('course_id', $activity->course_id)
            ->where('is_active', true)
            ->get()
            ->map(fn (CourseStudent $enrollment) => [
                'student_id' => $enrollment->student_id,
                'activity_id' => $activity->id,
                'score' => null,
                'status' => 'incomplete',
                'file' => '',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->all();

        ActivityLog::query()->insert($logs);

        return ApiResponse::created(['message' => 'Activity created', 'id' => $activity->id]);
    }

    public function show(int $pk)
    {
        $activity = Activity::query()->with('activityType', 'teacher', 'course', 'term')->find($pk);
        if (! $activity) {
            return ApiResponse::notFound('Activity not found');
        }

        $logs = ActivityLog::query()
            ->where('activity_id', $activity->id)
            ->where('is_active', true)
            ->with('student.user', 'activity.activityType', 'activity.teacher', 'activity.course', 'activity.term')
            ->get();

        return ApiResponse::ok([
            'activity' => CoreTransformer::activity($activity),
            'logs' => $logs->map(fn (ActivityLog $log) => CoreTransformer::activityLog($log))->all(),
        ]);
    }

    public function log(Request $request, int $pk)
    {
        $activity = Activity::query()->with('activityType', 'course')->find($pk);
        if (! $activity) {
            return ApiResponse::notFound('Activity not found');
        }

        $entries = (array) $request->input('entries', []);
        $type = $activity->activityType->type;

        if ($type === 'value') {
            foreach ($entries as $entry) {
                $score = $entry['score'] ?? null;
                if ($score !== null && $activity->total !== null && (float) $score > (float) $activity->total) {
                    return ApiResponse::error("Mark {$score} exceeds total {$activity->total}");
                }
            }

            foreach ($entries as $entry) {
                $log = ActivityLog::query()->firstOrNew([
                    'activity_id' => $activity->id,
                    'student_id' => $entry['student_id'] ?? null,
                ]);
                $log->fill([
                    'score' => $entry['score'] ?? null,
                    'status' => isset($entry['score']) && $entry['score'] !== null ? 'complete' : 'incomplete',
                    'is_active' => true,
                ]);
                $log->save();
            }
        } elseif ($type === 'boolean') {
            $checkedIds = collect($entries)
                ->filter(fn ($entry) => ! empty($entry['checked']))
                ->pluck('student_id')
                ->filter()
                ->all();

            $studentIds = CourseStudent::query()
                ->where('course_id', $activity->course_id)
                ->where('is_active', true)
                ->pluck('student_id');

            foreach ($studentIds as $studentId) {
                $log = ActivityLog::query()->firstOrNew([
                    'activity_id' => $activity->id,
                    'student_id' => $studentId,
                ]);
                $log->fill([
                    'score' => in_array($studentId, $checkedIds, true) ? 2 : 1,
                    'status' => 'complete',
                    'is_active' => true,
                ]);
                $log->save();
            }
        }

        return ApiResponse::message('Activity logged successfully');
    }
}
