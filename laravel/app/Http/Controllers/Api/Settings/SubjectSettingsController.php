<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Support\Api\ApiResponse;
use App\Support\Transformers\CoreTransformer;
use Illuminate\Http\Request;

class SubjectSettingsController extends Controller
{
    public function index()
    {
        return ApiResponse::ok(Subject::query()->orderBy('name')->get()->map(fn ($subject) => CoreTransformer::subject($subject))->all());
    }

    public function store(Request $request)
    {
        $subject = Subject::query()->create(['name' => (string) $request->input('name', '')]);
        return ApiResponse::created(['message' => 'Subject created', 'id' => $subject->id]);
    }

    public function update(Request $request, int $pk)
    {
        $subject = Subject::query()->find($pk);
        if (! $subject) {
            return ApiResponse::notFound();
        }

        $subject->update(['name' => (string) $request->input('name', $subject->name)]);
        return ApiResponse::message('Subject updated');
    }

    public function toggle(int $pk)
    {
        $subject = Subject::query()->find($pk);
        if (! $subject) {
            return ApiResponse::notFound();
        }

        $subject->update(['is_active' => ! $subject->is_active]);
        return ApiResponse::message('Subject status updated', ['is_active' => $subject->is_active]);
    }
}
