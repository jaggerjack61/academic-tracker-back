<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityType;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Subject;
use App\Models\Term;
use App\Support\Api\ApiResponse;
use App\Support\Transformers\CoreTransformer;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    public function grades()
    {
        return ApiResponse::ok(Grade::query()->where('is_active', true)->orderBy('name')->get()->map(fn ($item) => CoreTransformer::grade($item))->all());
    }

    public function subjects()
    {
        return ApiResponse::ok(Subject::query()->where('is_active', true)->orderBy('name')->get()->map(fn ($item) => CoreTransformer::subject($item))->all());
    }

    public function teachers()
    {
        return ApiResponse::ok(Profile::query()->where('type', 'teacher')->where('is_active', true)->with('user')->orderBy('first_name')->get()->map(fn ($item) => CoreTransformer::profile($item))->all());
    }

    public function students()
    {
        return ApiResponse::ok(Profile::query()->where('type', 'student')->where('is_active', true)->with('user')->orderBy('first_name')->get()->map(fn ($item) => CoreTransformer::profile($item))->all());
    }

    public function activityTypes()
    {
        return ApiResponse::ok(ActivityType::query()->where('is_active', true)->orderBy('name')->get()->map(fn ($item) => CoreTransformer::activityType($item))->all());
    }

    public function courses()
    {
        return ApiResponse::ok(Course::query()->where('is_active', true)->with(['grade', 'subject', 'teacherAssignments.teacher.user'])->orderBy('name')->get()->map(fn ($item) => CoreTransformer::course($item))->all());
    }

    public function roles()
    {
        return ApiResponse::ok(Role::query()->orderBy('name')->get()->map(fn ($item) => CoreTransformer::role($item))->all());
    }

    public function terms()
    {
        return ApiResponse::ok(Term::query()->where('is_active', true)->orderBy('name')->get()->map(fn ($item) => CoreTransformer::term($item))->all());
    }
}
