<?php

namespace App\Http\Controllers\Api\Collab;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Support\Api\ApiResponse;
use Illuminate\Http\Request;

class UserLookupController extends Controller
{
    public function index(Request $request)
    {
        $profile = $request->user()->profile;
        $query = Profile::query()->where('is_active', true)->where('id', '!=', $profile->id)->with('user');
        if ($profile->type === 'student') {
            $query->whereIn('type', ['teacher', 'student']);
        }
        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('first_name')->orderBy('last_name')->limit(50)->get()->map(fn ($item) => [
            'id' => $item->id,
            'full_name' => $item->full_name,
            'type' => $item->type,
            'email' => $item->user?->email,
        ])->all();

        return ApiResponse::ok($users);
    }
}
