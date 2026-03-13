<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Support\Api\ApiResponse;
use App\Support\Transformers\CoreTransformer;
use Illuminate\Http\Request;

class GradeSettingsController extends Controller
{
    public function index()
    {
        return ApiResponse::ok(Grade::query()->orderBy('name')->get()->map(fn ($grade) => CoreTransformer::grade($grade))->all());
    }

    public function store(Request $request)
    {
        $grade = Grade::query()->create(['name' => (string) $request->input('name', '')]);
        return ApiResponse::created(['message' => 'Grade created', 'id' => $grade->id]);
    }

    public function update(Request $request, int $pk)
    {
        $grade = Grade::query()->find($pk);
        if (! $grade) {
            return ApiResponse::notFound();
        }

        $grade->update(['name' => (string) $request->input('name', $grade->name)]);
        return ApiResponse::message('Grade updated');
    }

    public function toggle(int $pk)
    {
        $grade = Grade::query()->find($pk);
        if (! $grade) {
            return ApiResponse::notFound();
        }

        $grade->update(['is_active' => ! $grade->is_active]);
        return ApiResponse::message('Grade status updated', ['is_active' => $grade->is_active]);
    }
}
