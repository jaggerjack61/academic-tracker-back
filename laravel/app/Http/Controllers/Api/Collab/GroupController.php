<?php

namespace App\Http\Controllers\Api\Collab;

use App\Http\Controllers\Controller;
use App\Models\ChatGroup;
use App\Models\ChatMember;
use App\Models\ChatMessage;
use App\Models\Profile;
use App\Support\Api\ApiResponse;
use App\Support\Api\ManualPaginator;
use App\Support\Transformers\CollabTransformer;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $profile = $request->user()->profile;
        if ($profile->type === 'admin') {
            $memberGroupIds = ChatMember::query()->where('profile_id', $profile->id)->where('is_active', true)->pluck('group_id');
            $classGroupIds = ChatGroup::query()->where('is_class_group', true)->pluck('id');
            $groupIds = $memberGroupIds->merge($classGroupIds)->unique();
        } else {
            $groupIds = ChatMember::query()->where('profile_id', $profile->id)->where('is_active', true)->pluck('group_id');
        }

        $query = ChatGroup::query()
            ->whereIn('id', $groupIds)
            ->with(['course', 'latestMessage.sender'])
            ->withCount(['activeMembers as active_member_count'])
            ->withMax('messages', 'created_at')
            ->orderByDesc('messages_max_created_at')
            ->orderByDesc('created_at');

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        [$items, $total, $page, $pageSize] = ManualPaginator::fromQuery($query, $request, 30);

        return ApiResponse::paginated(
            $items->map(fn ($item) => CollabTransformer::group($item))->all(),
            $total,
            $page,
            $pageSize,
        );
    }

    public function store(Request $request)
    {
        $profile = $request->user()->profile;
        $name = trim((string) $request->input('name', ''));
        if ($name === '') {
            return ApiResponse::error('Group name is required');
        }

        $group = ChatGroup::query()->create([
            'name' => $name,
            'description' => trim((string) $request->input('description', '')),
            'is_class_group' => false,
            'created_by_profile_id' => $profile->id,
        ]);

        ChatMember::query()->create([
            'group_id' => $group->id,
            'profile_id' => $profile->id,
            'is_active' => true,
        ]);

        foreach ((array) $request->input('member_ids', []) as $profileId) {
            if ((int) $profileId === $profile->id) {
                continue;
            }

            $memberProfile = Profile::query()->where('id', $profileId)->where('is_active', true)->first();
            if (! $memberProfile) {
                continue;
            }

            ChatMember::query()->firstOrCreate(
                ['group_id' => $group->id, 'profile_id' => $memberProfile->id],
                ['is_active' => true]
            );
        }

        $group->load(['course', 'latestMessage.sender']);

        return ApiResponse::created(CollabTransformer::group($group));
    }

    public function show(Request $request, int $pk)
    {
        $profile = $request->user()->profile;
        $group = ChatGroup::query()
            ->with(['course', 'latestMessage.sender'])
            ->withCount(['activeMembers as active_member_count'])
            ->find($pk);

        if (! $group) {
            return ApiResponse::notFound('Group not found');
        }

        $membership = $this->resolveMembership($group, $profile);

        if (! $membership) {
            return ApiResponse::forbidden('Not a member of this group');
        }

        $messagesQuery = ChatMessage::query()
            ->where('group_id', $group->id)
            ->where('created_at', '>=', $membership->joined_at)
            ->with('sender')
            ->orderByDesc('created_at');

        [$items, $total, $page, $pageSize] = ManualPaginator::fromQuery($messagesQuery, $request, 30);
        $members = ChatMember::query()->where('group_id', $group->id)->where('is_active', true)->with('profile.user')->get();

        return ApiResponse::ok([
            'group' => CollabTransformer::group($group),
            'members' => $members->map(fn ($member) => CollabTransformer::member($member))->all(),
            'messages' => [
                'results' => $items->reverse()->values()->map(fn ($message) => CollabTransformer::message($message))->all(),
                'total' => $total,
                'page' => $page,
                'page_size' => $pageSize,
            ],
        ]);
    }

    public function messages(Request $request, int $pk)
    {
        $profile = $request->user()->profile;
        $group = ChatGroup::query()->find($pk);

        if (! $group) {
            return ApiResponse::notFound('Group not found');
        }

        $membership = $this->resolveMembership($group, $profile);
        if (! $membership) {
            return ApiResponse::forbidden('Not a member of this group');
        }

        $baseQuery = ChatMessage::query()
            ->where('group_id', $group->id)
            ->where('created_at', '>=', $membership->joined_at);

        $afterId = max((int) $request->query('after_id', 0), 0);
        $messagesQuery = (clone $baseQuery)->with('sender');

        if ($afterId > 0) {
            $messagesQuery->where('id', '>', $afterId)->orderBy('id');
        } else {
            $messagesQuery->orderByDesc('id')->limit(50);
        }

        $messages = $messagesQuery->get();
        if ($afterId === 0) {
            $messages = $messages->reverse()->values();
        }

        return ApiResponse::ok([
            'messages' => $messages->map(fn (ChatMessage $message) => CollabTransformer::message($message))->all(),
            'total' => (clone $baseQuery)->count(),
            'latest_message_id' => (int) ((clone $baseQuery)->max('id') ?? 0),
        ]);
    }

    public function sendMessage(Request $request, int $pk)
    {
        $profile = $request->user()->profile;
        $group = ChatGroup::query()->find($pk);
        if (! $group) {
            return ApiResponse::notFound('Group not found');
        }

        $isMember = $this->resolveMembership($group, $profile) !== null;

        if (! $isMember) {
            return ApiResponse::forbidden('Not a member of this group');
        }

        $content = trim((string) $request->input('content', ''));
        if ($content === '') {
            return ApiResponse::error('Message content is required');
        }

        $message = ChatMessage::query()->create([
            'group_id' => $group->id,
            'sender_profile_id' => $profile->id,
            'content' => $content,
        ]);
        $message->load('sender');

        return ApiResponse::created(CollabTransformer::message($message));
    }

    public function addMembers(Request $request, int $pk)
    {
        $profile = $request->user()->profile;
        $group = ChatGroup::query()->find($pk);
        if (! $group) {
            return ApiResponse::notFound('Group not found');
        }
        if ($group->is_class_group && ! in_array($profile->type, ['admin', 'teacher'], true)) {
            return ApiResponse::forbidden('Only staff can modify class groups');
        }
        if (! $group->is_class_group && $group->created_by_profile_id !== $profile->id && $profile->type !== 'admin') {
            return ApiResponse::forbidden('Only the group creator or admin can add members');
        }

        $added = 0;
        foreach ((array) $request->input('member_ids', []) as $profileId) {
            $memberProfile = Profile::query()->where('id', $profileId)->where('is_active', true)->first();
            if (! $memberProfile) {
                continue;
            }

            $membership = ChatMember::query()->firstOrCreate(
                ['group_id' => $group->id, 'profile_id' => $memberProfile->id],
                ['is_active' => true]
            );
            if (! $membership->wasRecentlyCreated) {
                $membership->update(['is_active' => true]);
            }
            $added++;
        }

        return ApiResponse::ok(['added' => $added]);
    }

    public function removeMember(Request $request, int $pk)
    {
        $profile = $request->user()->profile;
        $group = ChatGroup::query()->find($pk);
        if (! $group) {
            return ApiResponse::notFound('Group not found');
        }
        if ($group->is_class_group && ! in_array($profile->type, ['admin', 'teacher'], true)) {
            return ApiResponse::forbidden('Only staff can modify class groups');
        }
        if (! $group->is_class_group && $group->created_by_profile_id !== $profile->id && $profile->type !== 'admin') {
            return ApiResponse::forbidden('Only the group creator or admin can remove members');
        }

        ChatMember::query()
            ->where('group_id', $group->id)
            ->where('profile_id', $request->input('profile_id'))
            ->update(['is_active' => false]);

        return ApiResponse::message('Member removed');
    }

    private function resolveMembership(ChatGroup $group, Profile $profile): ?ChatMember
    {
        $membership = ChatMember::query()
            ->where('group_id', $group->id)
            ->where('profile_id', $profile->id)
            ->where('is_active', true)
            ->first();

        if (! $membership && $profile->type === 'admin' && $group->is_class_group) {
            $membership = ChatMember::query()->firstOrCreate(
                ['group_id' => $group->id, 'profile_id' => $profile->id],
                ['is_active' => true]
            );

            if (! $membership->is_active) {
                $membership->update(['is_active' => true]);
            }
        }

        return $membership;
    }
}
