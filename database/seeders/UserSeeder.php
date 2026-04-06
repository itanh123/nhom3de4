<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ],
            [
                'name' => 'Teacher One',
                'email' => 'teacher1@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_TEACHER,
            ],
            [
                'name' => 'Teacher Two',
                'email' => 'teacher2@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_TEACHER,
            ],
            [
                'name' => 'Student One',
                'email' => 'student1@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_STUDENT,
            ],
            [
                'name' => 'Student Two',
                'email' => 'student2@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_STUDENT,
            ],
            [
                'name' => 'Student Three',
                'email' => 'student3@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_STUDENT,
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
