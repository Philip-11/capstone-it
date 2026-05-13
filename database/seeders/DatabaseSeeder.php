<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'teacher',
        ]);

        $user = User::first();

        User::create([
            'name' => 'Admin Teacher',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
            'role' => 'teacher',
        ]);

        User::create([
            'name' => 'Student',
            'email' => 'student@example.com',
            'password' => bcrypt('password'),
            'role' => 'student,'
        ]);

        Subject::create([
            'name' => 'Web Development',
            'subject_code' => 'WEB101',
            'user_id' => $user->id,
        ]);

        Subject::create([
            'name' => 'Mobile Development',
            'subject_code' => 'MOBDEV101',
            'user_id' => $user->id,
        ]);
    }
}
