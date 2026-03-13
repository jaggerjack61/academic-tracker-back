<?php

namespace App\Support\Auth;

use App\Models\Role;
use App\Models\User;
use App\Support\Transformers\CoreTransformer;

class AuthPayload
{
    public static function fromUser(User $user): array
    {
        $profile = $user->profile;
        $role = $profile ? Role::query()->where('name', $profile->type)->first() : null;

        return [
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->username,
            ],
            'profile' => $profile ? CoreTransformer::profile($profile) : null,
            'role' => $role?->name ?: $profile?->type,
        ];
    }
}