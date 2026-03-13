<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use App\Support\Api\ApiResponse;
use App\Support\Api\ManualPaginator;
use App\Support\Transformers\CoreTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSettingsController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->query('search', '');
        $query = Profile::query()->with('user');
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('id_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('email', 'like', "%{$search}%"));
            });
        }

        $query->latest();
        [$profiles, $total, $page, $pageSize] = ManualPaginator::fromQuery($query, $request, 10);

        return ApiResponse::paginated(
            $profiles->map(fn (Profile $profile) => CoreTransformer::profile($profile))->all(),
            $total,
            $page,
            $pageSize,
        );
    }

    public function store(Request $request)
    {
        $email = (string) $request->input('email', '');
        $roleName = (string) $request->input('role', '');
        $fullName = (string) $request->input('name', '');

        if (User::query()->where('email', $email)->exists()) {
            return ApiResponse::error('Email already exists');
        }

        $idNumber = (string) $request->input('id_number', '');
        if ($idNumber === '') {
            $idNumber = strtoupper(substr((string) Str::uuid(), 0, 8));
        }
        if (Profile::query()->where('id_number', $idNumber)->exists()) {
            return ApiResponse::error('ID number already exists');
        }

        [$firstName, $lastName] = $this->splitName($fullName);

        $user = User::query()->create([
            'name' => $email,
            'username' => $email,
            'email' => $email,
            'password' => Hash::make($email),
            'is_active' => true,
        ]);

        Profile::query()->create([
            'type' => $roleName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'dob' => (string) $request->input('dob', ''),
            'sex' => (string) $request->input('sex', 'male'),
            'phone_number' => (string) $request->input('phone_number', ''),
            'id_number' => $idNumber,
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        Role::query()->firstOrCreate(['name' => $roleName]);

        return ApiResponse::created(['message' => 'User created']);
    }

    public function update(Request $request, int $pk)
    {
        $profile = Profile::query()->find($pk);
        if (! $profile) {
            return ApiResponse::notFound('User not found');
        }

        [$firstName, $lastName] = $this->splitName((string) $request->input('name', $profile->full_name));
        $profile->first_name = $firstName;
        $profile->last_name = $lastName;
        if ($request->filled('role')) {
            $profile->type = (string) $request->input('role');
        }
        $profile->dob = (string) $request->input('dob', $profile->dob);
        $profile->sex = (string) $request->input('sex', $profile->sex);
        $profile->phone_number = (string) $request->input('phone_number', $profile->phone_number);
        $profile->id_number = (string) $request->input('id_number', $profile->id_number);
        $profile->save();

        $user = $profile->user;
        if ($request->filled('email')) {
            $email = (string) $request->input('email');
            $user->username = $email;
            $user->email = $email;
            $user->name = $email;
        }
        $user->save();

        return ApiResponse::message('User updated');
    }

    public function toggleStatus(int $pk)
    {
        $profile = Profile::query()->find($pk);
        if (! $profile) {
            return ApiResponse::notFound('User not found');
        }

        $profile->update(['is_active' => ! $profile->is_active]);

        return ApiResponse::message('User status updated', ['is_active' => $profile->is_active]);
    }

    private function splitName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName), 2, PREG_SPLIT_NO_EMPTY) ?: [];
        if (count($parts) > 1) {
            $lastSpace = strrpos($fullName, ' ');
            return [substr($fullName, 0, $lastSpace), substr($fullName, $lastSpace + 1)];
        }

        return [$fullName, ''];
    }
}
