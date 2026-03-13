<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseStudent;
use App\Models\CourseTeacher;
use App\Models\Profile;
use App\Support\Api\ApiResponse;
use App\Support\Transformers\CoreTransformer;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->query('search', '');
        $query = Profile::query()->where('type', 'teacher')->with('user');
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        return ApiResponse::ok(
            $query->latest()->get()->map(fn (Profile $teacher) => CoreTransformer::profile($teacher))->all()
        );
    }

    public function show(int $pk)
    {
        $teacher = Profile::query()->where('id', $pk)->where('type', 'teacher')->with('user')->first();
        if (! $teacher) {
            return ApiResponse::notFound('Teacher not found');
        }

        $classes = CourseTeacher::query()
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->with('course.grade', 'course.subject')
            ->get()
            ->map(fn (CourseTeacher $assignment) => [
                'course_id' => $assignment->course_id,
                'course_name' => $assignment->course?->name,
                'grade' => $assignment->course?->grade?->name,
                'subject' => $assignment->course?->subject?->name,
                'student_count' => CourseStudent::query()->where('course_id', $assignment->course_id)->where('is_active', true)->count(),
            ])
            ->all();

        return ApiResponse::ok([
            'teacher' => CoreTransformer::profile($teacher),
            'classes' => $classes,
        ]);
    }
}
