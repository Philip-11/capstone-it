<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    public function index()
    {
        return Inertia::render('Teacher/Dashboard', [
            'lessons' => Auth::user()->lessons()->with('quiz')->latest()->get(),
        ]);
    }

    public function storeLesson(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Auth::user()->lessons()->create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'content' => $validated['content'],
        ]);

        return redirect()->back()->with('message', 'Lesson created!');
    }


    public function storeQuiz(Request $request, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'passing_score' => 'required|integer|min:0|max:100',
        ]);

        $lesson->quiz()->create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'passing_score' => $request->passing_score,
        ]);

        return redirect()->back()->with('message', 'Quiz linked to lesson!');
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_text'  => 'required|string',
            'options'        => 'required|array|min:2', // Vue will send this as an array
            'correct_answer' => 'required|string',
            'points'         => 'integer|min:1',
        ]);

        $quiz->questions()->create($validated);

        return redirect()->back()->with('message', 'Question added!');
    }

    /**
     * 4. AI INSIGHTS (The "See student's progress" part)
     */
    public function viewStudentProgress()
    {
        // Fetch all students who have interacted with this teacher's content
        // This is a bit more complex, but start with a simple list of all students
        return Inertia::render('Teacher/StudentProgress', [
            'reports' => \App\Models\Report::with('student')->latest()->get()
        ]);
    }

}
