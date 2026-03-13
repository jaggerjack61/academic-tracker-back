<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\CourseTeacher;
use App\Support\Api\ApiResponse;
use App\Support\Api\ManualPaginator;
use App\Support\Collab\ClassGroupSyncService;
use App\Support\Transformers\CoreTransformer;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->query('search', '');
        $query = Course::query()->with(['grade', 'subject', 'teacherAssignments.teacher.user']);
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhereHas('grade', fn ($gradeQuery) => $gradeQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('subject', fn ($subjectQuery) => $subjectQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('teacherAssignments.teacher', function ($teacherQuery) use ($search): void {
                        $teacherQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            })->distinct();
        }

        $query->latest();
        [$courses, $total, $page, $pageSize] = ManualPaginator::fromQuery($query, $request, 10);

        return ApiResponse::paginated(
            $courses->map(fn (Course $course) => CoreTransformer::course($course))->all(),
            $total,
            $page,
            $pageSize,
        );
    }

    public function store(Request $request)
    {
        $course = Course::query()->create([
            'name' => (string) $request->input('name', ''),
            'code' => $request->filled('code') ? $request->input('code') : null,
            'description' => (string) $request->input('description', ''),
            'grade_id' => $request->input('grade_id'),
            'subject_id' => $request->input('subject_id'),
        ]);

        if ($request->filled('teacher_id')) {
            CourseTeacher::query()->create([
                'course_id' => $course->id,
                'teacher_id' => $request->input('teacher_id'),
                'is_active' => true,
            ]);
        }

        return ApiResponse::created(['message' => 'Class created', 'id' => $course->id]);
    }

    public function update(Request $request, int $pk)
    {
        $course = Course::query()->find($pk);
        if (! $course) {
            return ApiResponse::notFound('Class not found');
        }

        $course->name = (string) $request->input('name', $course->name);
        $course->code = $request->filled('code') ? $request->input('code') : $course->code;
        $course->description = (string) $request->input('description', $course->description);
        $course->grade_id = $request->input('grade_id', $course->grade_id);
        $course->subject_id = $request->input('subject_id', $course->subject_id);
        $course->save();

        if ($request->filled('teacher_id')) {
            CourseTeacher::query()->where('course_id', $course->id)->where('is_active', true)->update(['is_active' => false]);
            CourseTeacher::query()->create([
                'course_id' => $course->id,
                'teacher_id' => $request->input('teacher_id'),
                'is_active' => true,
            ]);
        }

        return ApiResponse::message('Class updated');
    }

    public function toggleStatus(int $pk)
    {
        $course = Course::query()->find($pk);
        if (! $course) {
            return ApiResponse::notFound('Class not found');
        }

        $course->update(['is_active' => ! $course->is_active]);

        return ApiResponse::message('Class status updated', ['is_active' => $course->is_active]);
    }

    public function show(int $pk)
    {
        $course = Course::query()->with(['grade', 'subject', 'teacherAssignments.teacher.user'])->find($pk);
        if (! $course) {
            return ApiResponse::notFound('Class not found');
        }

        $students = CourseStudent::query()
            ->where('course_id', $course->id)
            ->where('is_active', true)
            ->with('student.user')
            ->get()
            ->map(fn (CourseStudent $enrollment) => [
                'enrollment_id' => $enrollment->id,
                'student' => CoreTransformer::profile($enrollment->student),
            ])
            ->all();

        $teacherAssignment = CourseTeacher::query()
            ->where('course_id', $course->id)
            ->where('is_active', true)
            ->with('teacher.user')
            ->first();

        return ApiResponse::ok([
            'course' => CoreTransformer::course($course),
            'teacher' => $teacherAssignment?->teacher ? CoreTransformer::profile($teacherAssignment->teacher) : null,
            'students' => $students,
        ]);
    }

    public function enrollStudents(Request $request, int $pk)
    {
        $course = Course::query()->find($pk);
        if (! $course) {
            return ApiResponse::notFound('Class not found');
        }

        foreach ((array) $request->input('student_ids', []) as $studentId) {
            $enrollment = CourseStudent::query()->firstOrCreate(
                ['course_id' => $course->id, 'student_id' => $studentId],
                ['is_active' => true]
            );

            if (! $enrollment->is_active) {
                $enrollment->update(['is_active' => true]);
            }
        }

        ClassGroupSyncService::sync($course);

        return ApiResponse::message('Students enrolled');
    }

    public function unenrollStudent(Request $request, int $pk)
    {
        $enrollment = CourseStudent::query()
            ->where('course_id', $pk)
            ->where('student_id', $request->input('student_id'))
            ->where('is_active', true)
            ->first();

        if (! $enrollment) {
            return ApiResponse::notFound('Enrollment not found');
        }

        $enrollment->update(['is_active' => false]);
        ClassGroupSyncService::sync(Course::query()->findOrFail($pk));

        return ApiResponse::message('Student un-enrolled');
    }

    public function copyRoster(Request $request, int $pk)
    {
        $sourceEnrollments = CourseStudent::query()->where('course_id', $pk)->where('is_active', true)->get();
        foreach ((array) $request->input('destination_course_ids', []) as $destinationId) {
            if (! Course::query()->find($destinationId)) {
                continue;
            }

            foreach ($sourceEnrollments as $enrollment) {
                $destination = CourseStudent::query()->firstOrCreate(
                    ['course_id' => $destinationId, 'student_id' => $enrollment->student_id],
                    ['is_active' => true]
                );

                if (! $destination->is_active) {
                    $destination->update(['is_active' => true]);
                }
            }
        }

        return ApiResponse::message('Roster copied');
    }

    public function moveRoster(Request $request, int $pk)
    {
        $sourceEnrollments = CourseStudent::query()->where('course_id', $pk)->where('is_active', true)->get();
        foreach ((array) $request->input('destination_course_ids', []) as $destinationId) {
            if (! Course::query()->find($destinationId)) {
                continue;
            }

            foreach ($sourceEnrollments as $enrollment) {
                $destination = CourseStudent::query()->firstOrCreate(
                    ['course_id' => $destinationId, 'student_id' => $enrollment->student_id],
                    ['is_active' => true]
                );

                if (! $destination->is_active) {
                    $destination->update(['is_active' => true]);
                }
            }
        }

        CourseStudent::query()->where('course_id', $pk)->where('is_active', true)->update(['is_active' => false]);

        return ApiResponse::message('Roster moved');
    }
}
