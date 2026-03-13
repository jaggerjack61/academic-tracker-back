<?php

namespace App\Http\Controllers\Api\Collab;

use App\Http\Controllers\Controller;
use App\Models\ChatGroup;
use App\Models\ChatMember;
use App\Models\Profile;
use App\Support\Api\ApiResponse;
use App\Support\Transformers\CollabTransformer;
use Illuminate\Http\Request;

class DmController extends Controller
{
    public function store(Request $request)
    {
        $profile = $request->user()->profile;
        $otherId = $request->input('profile_id');
        if (! $otherId || (int) $otherId === $profile->id) {
            return ApiResponse::error('Invalid recipient');
        }

        $other = Profile::query()->where('id', $otherId)->where('is_active', true)->first();
        if (! $other) {
            return ApiResponse::notFound('User not found');
        }

        $myGroupIds = ChatMember::query()->where('profile_id', $profile->id)->where('is_active', true)->pluck('group_id');
        $otherGroupIds = ChatMember::query()->where('profile_id', $other->id)->where('is_active', true)->pluck('group_id');
        foreach ($myGroupIds->intersect($otherGroupIds) as $groupId) {
            $group = ChatGroup::query()->find($groupId);
            if ($group && ! $group->is_class_group && $group->members()->where('is_active', true)->count() === 2) {
                return ApiResponse::ok(CollabTransformer::group($group->load(['course', 'messages.sender'])));
            }
        }

        $group = ChatGroup::query()->create([
            'name' => $profile->full_name.' & '.$other->full_name,
            'description' => '',
            'is_class_group' => false,
            'created_by_profile_id' => $profile->id,
        ]);

        ChatMember::query()->create(['group_id' => $group->id, 'profile_id' => $profile->id, 'is_active' => true]);
        ChatMember::query()->create(['group_id' => $group->id, 'profile_id' => $other->id, 'is_active' => true]);

        return ApiResponse::created(CollabTransformer::group($group->load(['course', 'messages.sender'])));
    }
}
