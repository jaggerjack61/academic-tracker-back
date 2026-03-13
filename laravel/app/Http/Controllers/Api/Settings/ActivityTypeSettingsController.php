<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Models\ActivityType;
use App\Support\Api\ApiResponse;
use App\Support\Transformers\CoreTransformer;
use Illuminate\Http\Request;

class ActivityTypeSettingsController extends Controller
{
    public function index()
    {
        return ApiResponse::ok(ActivityType::query()->orderBy('name')->get()->map(fn ($item) => CoreTransformer::activityType($item))->all());
    }

    public function store(Request $request)
    {
        if (! $request->filled('image')) {
            return ApiResponse::error('Image is required');
        }

        $activityType = ActivityType::query()->create([
            'name' => (string) $request->input('name', ''),
            'description' => (string) $request->input('description', ''),
            'type' => (string) $request->input('type', 'value'),
            'image' => (string) $request->input('image'),
            'true_value' => (string) $request->input('true_value', ''),
            'false_value' => (string) $request->input('false_value', ''),
        ]);

        return ApiResponse::created(['message' => 'Activity type created', 'id' => $activityType->id]);
    }

    public function update(Request $request, int $pk)
    {
        $activityType = ActivityType::query()->find($pk);
        if (! $activityType) {
            return ApiResponse::notFound();
        }

        $activityType->update([
            'name' => (string) $request->input('name', $activityType->name),
            'description' => (string) $request->input('description', $activityType->description),
            'image' => (string) $request->input('image', $activityType->image),
            'true_value' => (string) $request->input('true_value', $activityType->true_value),
            'false_value' => (string) $request->input('false_value', $activityType->false_value),
        ]);

        return ApiResponse::message('Activity type updated');
    }

    public function toggle(int $pk)
    {
        $activityType = ActivityType::query()->find($pk);
        if (! $activityType) {
            return ApiResponse::notFound();
        }

        $activityType->update(['is_active' => ! $activityType->is_active]);
        return ApiResponse::message('Activity type status updated', ['is_active' => $activityType->is_active]);
    }
}
