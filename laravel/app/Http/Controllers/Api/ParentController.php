<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Relationship;
use App\Support\Api\ApiResponse;
use App\Support\Transformers\CoreTransformer;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function index()
    {
        $parents = Profile::query()->where('type', 'parent')->where('is_active', true)->with('user')->latest()->get();

        return ApiResponse::ok($parents->map(fn (Profile $parent) => CoreTransformer::profile($parent))->all());
    }

    public function show(int $pk)
    {
        $parent = Profile::query()->where('id', $pk)->where('type', 'parent')->with('user')->first();
        if (! $parent) {
            return ApiResponse::notFound('Parent not found');
        }

        $linkedStudents = Relationship::query()
            ->where('parent_id', $parent->id)
            ->where('is_active', true)
            ->with('student.user')
            ->get()
            ->map(fn (Relationship $relationship) => [
                'relationship_id' => $relationship->id,
                'student' => CoreTransformer::profile($relationship->student),
            ])
            ->all();

        return ApiResponse::ok([
            'parent' => CoreTransformer::profile($parent),
            'linked_students' => $linkedStudents,
        ]);
    }

    public function linkStudents(Request $request, int $pk)
    {
        $parent = Profile::query()->where('id', $pk)->where('type', 'parent')->first();
        if (! $parent) {
            return ApiResponse::notFound('Parent not found');
        }

        $errors = [];
        foreach ((array) $request->input('student_ids', []) as $studentId) {
            $student = Profile::query()->where('id', $studentId)->where('type', 'student')->first();
            if (! $student) {
                continue;
            }

            $relationship = Relationship::query()->firstOrCreate(
                ['student_id' => $student->id, 'parent_id' => $parent->id],
                ['is_active' => true]
            );

            if ($relationship->wasRecentlyCreated) {
                continue;
            }

            if ($relationship->is_active) {
                $errors[] = "{$student->full_name} is already linked";
                continue;
            }

            $relationship->update(['is_active' => true]);
        }

        if ($errors) {
            return ApiResponse::ok([
                'message' => 'Some links already exist',
                'errors' => $errors,
            ], 400);
        }

        return ApiResponse::message('Students linked successfully');
    }

    public function unlinkStudent(Request $request, int $pk)
    {
        $relationship = Relationship::query()
            ->where('parent_id', $pk)
            ->where('student_id', $request->input('student_id'))
            ->where('is_active', true)
            ->first();

        if (! $relationship) {
            return ApiResponse::notFound('Relationship not found');
        }

        $relationship->update(['is_active' => false]);

        return ApiResponse::message('Student unlinked');
    }
}
