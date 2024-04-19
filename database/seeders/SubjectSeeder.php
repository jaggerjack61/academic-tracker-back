<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subject::create([
            'name'=> 'Art'
        ]);
        Subject::create([
            'name'=> 'English'
        ]);
        Subject::create([
            'name'=> 'Mathematics'
        ]);
        Subject::create([
            'name'=> 'Shona'
        ]);
    }
}
