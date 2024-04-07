<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Admin Person',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345'),
            'role_id' =>  1
        ]);

        Admin::create([
            'first_name' => 'Admin',
            'last_name' => 'Person',
            'dob' => '1990-01-01',
            'sex' => 'male',
            'id_number' => '00000',
            'phone_number' => '0000000000',
            'user_id' => $user->id,
        ]);

    }
}
