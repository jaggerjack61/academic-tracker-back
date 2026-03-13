<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Api\ApiResponse;
use App\Support\Auth\AuthPayload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $email = (string) $request->input('email', '');
        $password = (string) $request->input('password', '');

        $user = User::query()->where('email', $email)->first();
        if (! $user || ! Auth::attempt(['username' => $user->username, 'password' => $password])) {
            return ApiResponse::error('Invalid credentials', 401);
        }

        $request->session()->regenerate();

        return ApiResponse::ok(AuthPayload::fromUser($request->user()->load('profile.user')));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return ApiResponse::message('Logged out');
    }

    public function me(Request $request)
    {
        return ApiResponse::ok(AuthPayload::fromUser($request->user()->load('profile.user')));
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();
        $oldPassword = (string) $request->input('old_password', '');
        $newPassword = (string) $request->input('new_password', '');

        if (! Hash::check($oldPassword, $user->password)) {
            return ApiResponse::error('Invalid old password');
        }

        if ($oldPassword === $newPassword) {
            return ApiResponse::error('New password must differ from old password');
        }

        $user->password = Hash::make($newPassword);
        $user->save();
        Auth::login($user);

        return ApiResponse::message('Password changed successfully');
    }

    public function passwordReset(Request $request)
    {
        return ApiResponse::message('If the email exists, a reset link will be sent.');
    }
}
