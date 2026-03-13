<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\ChatGroup;
use App\Models\ChatMember;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BackendSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_seeded_admin_can_login_and_fetch_me(): void
    {
        $loginResponse = $this->postJson('/api/auth/login/', [
            'email' => 'admin@example.com',
            'password' => '12345',
        ]);

        $loginResponse
            ->assertOk()
            ->assertJsonPath('user.email', 'admin@example.com')
            ->assertJsonPath('role', 'admin');

        $this->getJson('/api/auth/me/')
            ->assertOk()
            ->assertJsonPath('profile.type', 'admin');
    }

    public function test_finance_dashboard_is_available_to_authenticated_students(): void
    {
        $user = User::query()->create([
            'name' => 'student@example.com',
            'username' => 'student@example.com',
            'email' => 'student@example.com',
            'password' => Hash::make('student@example.com'),
            'is_active' => true,
        ]);

        Profile::query()->create([
            'type' => 'student',
            'first_name' => 'Student',
            'last_name' => 'User',
            'dob' => '2005-01-01',
            'sex' => 'male',
            'phone_number' => '',
            'is_active' => true,
            'id_number' => 'STUDENT1',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->getJson('/api/finance/dashboard/')
            ->assertOk();
    }

    public function test_student_history_route_keeps_current_parameter_mismatch(): void
    {
        $user = User::query()->create([
            'name' => 'teacher@example.com',
            'username' => 'teacher@example.com',
            'email' => 'teacher@example.com',
            'password' => Hash::make('teacher@example.com'),
            'is_active' => true,
        ]);

        Profile::query()->create([
            'type' => 'teacher',
            'first_name' => 'Teacher',
            'last_name' => 'User',
            'dob' => '1990-01-01',
            'sex' => 'female',
            'phone_number' => '',
            'is_active' => true,
            'id_number' => 'TEACHER1',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->getJson('/api/students/999/activity-history/')
            ->assertNotFound()
            ->assertJson(['error' => 'Student not found']);
    }

    public function test_collab_message_sync_returns_only_new_messages(): void
    {
        $user = User::query()->create([
            'name' => 'teacher@example.com',
            'username' => 'teacher@example.com',
            'email' => 'teacher@example.com',
            'password' => Hash::make('teacher@example.com'),
            'is_active' => true,
        ]);

        $profile = Profile::query()->create([
            'type' => 'teacher',
            'first_name' => 'Teacher',
            'last_name' => 'User',
            'dob' => '1990-01-01',
            'sex' => 'female',
            'phone_number' => '',
            'is_active' => true,
            'id_number' => 'TEACHER2',
            'user_id' => $user->id,
        ]);

        $group = ChatGroup::query()->create([
            'name' => 'Staff room',
            'description' => '',
            'is_class_group' => false,
            'created_by_profile_id' => $profile->id,
        ]);

        ChatMember::query()->create([
            'group_id' => $group->id,
            'profile_id' => $profile->id,
            'is_active' => true,
        ]);

        $firstMessage = ChatMessage::query()->create([
            'group_id' => $group->id,
            'sender_profile_id' => $profile->id,
            'content' => 'First message',
        ]);

        $secondMessage = ChatMessage::query()->create([
            'group_id' => $group->id,
            'sender_profile_id' => $profile->id,
            'content' => 'Second message',
        ]);

        $this->actingAs($user)
            ->getJson("/api/collab/groups/{$group->id}/messages/?after_id={$firstMessage->id}")
            ->assertOk()
            ->assertJsonPath('messages.0.content', 'Second message')
            ->assertJsonPath('latest_message_id', $secondMessage->id)
            ->assertJsonPath('total', 2)
            ->assertJsonCount(1, 'messages');
    }
}