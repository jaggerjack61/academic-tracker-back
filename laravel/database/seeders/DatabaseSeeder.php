<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use App\Models\FeeType;
use App\Models\Grade;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (['admin', 'student', 'teacher', 'parent'] as $roleName) {
            Role::query()->firstOrCreate(['name' => $roleName]);
        }

        $adminUser = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'admin@example.com',
                'username' => 'admin@example.com',
                'password' => Hash::make('12345'),
                'is_active' => true,
            ]
        );

        Profile::query()->firstOrCreate(
            ['user_id' => $adminUser->id],
            [
                'type' => 'admin',
                'first_name' => 'Admin',
                'last_name' => 'Person',
                'dob' => '1990-01-01',
                'sex' => 'male',
                'phone_number' => '0000000000',
                'id_number' => '00000',
                'is_active' => true,
            ]
        );

        foreach (['Grade 1', 'Grade 2', 'Grade 3', 'Form 1', 'Form 2', 'Form 3'] as $gradeName) {
            Grade::query()->firstOrCreate(['name' => $gradeName], ['is_active' => true]);
        }

        foreach (['Art', 'English', 'Mathematics', 'Shona'] as $subjectName) {
            Subject::query()->firstOrCreate(['name' => $subjectName], ['is_active' => true]);
        }

        Term::query()->firstOrCreate(
            ['name' => '2024 Term 1'],
            ['start' => '2024-01-01', 'end' => '2024-04-30', 'is_active' => true]
        );

        ActivityType::query()->firstOrCreate(
            ['name' => 'Homework'],
            [
                'description' => 'You will have no free time under our care. You are welcome',
                'type' => 'value',
                'image' => 'homework.png',
                'is_active' => true,
                'true_value' => '',
                'false_value' => '',
            ]
        );

        ActivityType::query()->firstOrCreate(
            ['name' => 'Attendance'],
            [
                'description' => 'Why were you absent?What do you mean the dog swallowed your bus fare?',
                'type' => 'boolean',
                'image' => 'attendance.png',
                'is_active' => true,
                'true_value' => 'Present',
                'false_value' => 'Absent',
            ]
        );

        ActivityType::query()->firstOrCreate(
            ['name' => 'Study Material'],
            [
                'description' => 'I know you are not gonna read these anyway but as your teacher its my job to give these to you.',
                'type' => 'static',
                'image' => 'study_material.png',
                'is_active' => true,
                'true_value' => '',
                'false_value' => '',
            ]
        );

        foreach ([
            ['name' => 'Tuition', 'description' => 'Standard tuition fees for the term'],
            ['name' => 'Registration', 'description' => 'One-time registration fee'],
            ['name' => 'Laboratory', 'description' => 'Science lab usage fees'],
            ['name' => 'Sports', 'description' => 'Sports and extracurricular activities'],
            ['name' => 'Library', 'description' => 'Library access and resources'],
        ] as $feeType) {
            FeeType::query()->firstOrCreate(
                ['name' => $feeType['name']],
                ['description' => $feeType['description'], 'is_active' => true]
            );
        }
    }
}
