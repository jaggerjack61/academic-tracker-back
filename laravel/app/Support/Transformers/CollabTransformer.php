<?php

namespace App\Support\Transformers;

use App\Models\ChatGroup;
use App\Models\ChatMember;
use App\Models\ChatMessage;
use Illuminate\Support\Str;

class CollabTransformer
{
    public static function member(ChatMember $member): array
    {
        $member->loadMissing('profile.user');

        return [
            'id' => $member->id,
            'profile' => $member->profile ? CoreTransformer::profile($member->profile) : null,
            'joined_at' => optional($member->joined_at)?->toISOString(),
            'is_active' => (bool) $member->is_active,
        ];
    }

    public static function group(ChatGroup $group): array
    {
        $group->loadMissing(['course', 'latestMessage.sender']);
        $lastMessage = $group->latestMessage;
        $memberCount = $group->getAttribute('active_member_count');

        if ($memberCount === null) {
            $memberCount = $group->activeMembers()->count();
        }

        return [
            'id' => $group->id,
            'name' => $group->name,
            'description' => $group->description,
            'is_class_group' => (bool) $group->is_class_group,
            'created_by' => $group->created_by_profile_id,
            'course' => $group->course_id,
            'course_name' => $group->course?->name,
            'member_count' => (int) $memberCount,
            'last_message' => $lastMessage ? [
                'id' => $lastMessage->id,
                'sender_name' => $lastMessage->sender?->full_name,
                'content' => Str::limit($lastMessage->content, 80, ''),
                'created_at' => optional($lastMessage->created_at)?->toISOString(),
            ] : null,
            'created_at' => optional($group->created_at)?->toISOString(),
        ];
    }

    public static function message(ChatMessage $message): array
    {
        $message->loadMissing('sender');

        return [
            'id' => $message->id,
            'group' => $message->group_id,
            'sender' => $message->sender_id,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender?->full_name,
            'sender_type' => $message->sender?->type,
            'content' => $message->content,
            'created_at' => optional($message->created_at)?->toISOString(),
        ];
    }
}