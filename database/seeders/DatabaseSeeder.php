<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
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
            'role' => 'student'
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

        Lesson::create([
            'user_id' => $user->id,
            'title' => 'test lesson',
            'slug' => 'test-lesson',
            'content' => 'testing',
            'subject_id' => 1,
        ]);

        $teacher = User::where('role', '=', 'teacher')->first() ?? User::first();
        $lesson = Lesson::first();

        if (!$lesson){
            $this->command->warn('Warning: Make a lesson first before you run this seed so you the quiz will have a lesson to lean on');
            return;
        }

        $quiz = Quiz::create([
            'user_id' => $teacher->id,
            'lesson_id' => $lesson->id,
            'title' => 'Web Development Core Basics',
            'passing_score' => 70,
        ]);

        Question::create([
        'quiz_id' => $quiz->id,
        'question_text' => 'Which programming language is mainly used for building the structure of a webpage?',
        'question_type' => 'multiple_choice',
        'options' => ['HTML', 'CSS', 'JavaScript', 'PHP'], // Naka-array lang, si Laravel na bahala sa JSON
        'correct_answer' => 'HTML',
        'points' => 1,
        'explanation' => 'HTML stands for HyperText Markup Language and provides the basic skeletal structure of a website.',
    ]);

        // 4. Gawa ng True or False Question
        Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'Vue 3 uses the Options API natively, but introduces the Composition API as a more powerful alternative.',
            'question_type' => 'true_or_false',
            'options' => ['True', 'False'],
            'correct_answer' => 'True',
            'points' => 1,
            'explanation' => 'Composition API was introduced in Vue 3 to allow better logic reuse and code organization.',
        ]);

        // 5. Gawa ng Short Answer / Identification Question
        Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'What does PHP stand for? (Acronym hint: Hypertext Preprocessor)',
            'question_type' => 'short_answer',
            'options' => null, // Walang choices kapag identification
            'correct_answer' => 'PHP: Hypertext Preprocessor',
            'points' => 2, // Mas mataas ang points nito
            'explanation' => 'PHP originally stood for Personal Home Page, but now it uses a recursive acronym: PHP: Hypertext Preprocessor.',
        ]);

        $this->command->info('Quiz and Questions seeded successfully! Ready for student testing.');
    }
}
