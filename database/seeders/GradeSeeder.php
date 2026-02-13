<?php

namespace Database\Seeders;

use App\Models\Grade;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Grade::create([
            'name' => 'Grade 1',
        ]);
        Grade::create([
            'name' => 'Grade 2',
        ]);
        Grade::create([
            'name' => 'Grade 3',
        ]);
        Grade::create([
            'name' => 'Form 1',
        ]);
        Grade::create([
            'name' => 'Form 2',
        ]);
        Grade::create([
            'name' => 'Form 3',
        ]);
    }
}
