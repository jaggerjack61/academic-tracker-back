<?php

namespace App\Support\Collab;

use App\Models\ChatGroup;
use App\Models\ChatMember;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\CourseTeacher;

class ClassGroupSyncService
{
    public static function sync(Course $course): ChatGroup
    {
        $group = ChatGroup::query()->firstOrCreate(
            ['course_id' => $course->id],
            [
                'name' => $course->name,
                'description' => '',
                'is_class_group' => true,
            ]
        );

        if ($group->name !== $course->name) {
            $group->update(['name' => $course->name]);
        }

        $teacherIds = CourseTeacher::query()
            ->where('course_id', $course->id)
            ->where('is_active', true)
            ->pluck('teacher_id')
            ->all();

        $studentIds = CourseStudent::query()
            ->where('course_id', $course->id)
            ->where('is_active', true)
            ->pluck('student_id')
            ->all();

        $expectedIds = collect(array_merge($teacherIds, $studentIds))->unique()->values();
        $currentIds = ChatMember::query()
            ->where('group_id', $group->id)
            ->where('is_active', true)
            ->pluck('profile_id');

        foreach ($expectedIds->diff($currentIds) as $profileId) {
            $member = ChatMember::query()->firstOrCreate(
                ['group_id' => $group->id, 'profile_id' => $profileId],
                ['is_active' => true]
            );

            if (! $member->is_active) {
                $member->update(['is_active' => true]);
            }
        }

        ChatMember::query()
            ->where('group_id', $group->id)
            ->where('is_active', true)
            ->whereNotIn('profile_id', $expectedIds)
            ->update(['is_active' => false]);

        return $group;
    }
}