<?php

namespace App\Http\Controllers\Api\Collab;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Support\Api\ApiResponse;
use App\Support\Collab\ClassGroupSyncService;
use Illuminate\Http\Request;

class ClassGroupSyncController extends Controller
{
    public function syncAll(Request $request)
    {
        $profile = $request->user()->profile;
        if (! in_array($profile->type, ['admin', 'teacher'], true)) {
            return ApiResponse::forbidden('Permission denied');
        }

        $count = 0;
        foreach (Course::query()->where('is_active', true)->get() as $course) {
            ClassGroupSyncService::sync($course);
            $count++;
        }

        return ApiResponse::ok(['synced' => $count]);
    }
}
