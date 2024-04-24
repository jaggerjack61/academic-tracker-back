<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ActivityType::create([
            'name' => 'Homework',
            'description' => 'You will have no free time under our care. You are welcome',
            'type' => 'value',
            'image' => 'asset/img/backgrounds/18.jpg'
        ]);

        ActivityType::create([
            'name' => 'Attendance',
            'description' => 'Why were you absent?What do you mean the dog swallowed your bus fare?',
            'type' => 'boolean',
            'image' => 'asset/img/backgrounds/18.jpg',
            'true_value' => 'Present',
            'false_value' => 'Absent'
        ]);

        ActivityType::create([
            'name' => 'Study Material',
            'description' => 'I know you are not gonna read these anyway but as your teacher its my job to give these to you.',
            'type' => 'static',
            'image' => 'asset/img/backgrounds/18.jpg'
        ]);
    }
}
