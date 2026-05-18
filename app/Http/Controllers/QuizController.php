<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class QuizController extends Controller
{
    // 1. LISTAHAN NG MGA QUIZZES NG TEACHER
    public function index()
    {
        $quizzes = Quiz::where('user_id', Auth::id())
            ->withCount('questions')
            ->latest()
            ->get();

        return Inertia::render('Teacher/Quizzes/Index', [
            'quizzes' => $quizzes
        ]);
    }

    // 2. FORM PARA SA PAGGAWA NG QUIZ
    public function create()
    {
        // Kukunin natin ang mga lessons ng teacher para pwede niyang i-attach agad ang quiz sa lesson kung gusto niya
        $lessons = Lesson::whereHas('subject', function($query) {
            $query->where('user_id', Auth::id());
        })->get();

        return Inertia::render('Teacher/Quizzes/Create', [
            'lessons' => $lessons
        ]);
    }

    // 3. PAG-SAVE NG QUIZ AT MGA TANONG (Ang Magic Core Method!)
    public function store(Request $request)
    {
        // Validate muna natin para siguradong may data
        $request->validate([
            'title' => 'required|string|max:255',
            'passing_score' => 'required|integer|min:1|max:100',
            'lesson_id' => 'nullable|exists:lessons,id',
            'questions' => 'required|array|min:1', // Dapat may kahit isang tanong
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|string',
            'questions.*.correct_answer' => 'required|string',
            'questions.*.points' => 'required|integer|min:1',
        ]);

        // A. I-save muna ang Quiz Meta Data
        $quiz = Quiz::create([
            'user_id' => Auth::id(),
            'lesson_id' => $request->lesson_id,
            'title' => $request->title,
            'passing_score' => $request->passing_score,
        ]);

        // B. I-loop ang mga tanong galing sa Vue at i-save sa questions table
        foreach ($request->questions as $q) {
            Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => $q['question_text'],
                'question_type' => $q['question_type'],
                'options' => $q['options'] ?? null, // Automatic na 'tong naka-cast into JSON basta naka-array sa PHP
                'correct_answer' => $q['correct_answer'],
                'points' => $q['points'] ?? 1,
                'explanation' => $q['explanation'] ?? null,
            ]);
        }

        return redirect()->route('teacher.quizzes.index')
            ->with('message', 'Quiz and questions created successfully!');
    }

    // 4. PAG-DELETE NG QUIZ
    public function destroy(Quiz $quiz)
    {
        // Siguraduhin na ang nagmamay-ari na teacher ang nagbubura
        if ($quiz->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Dahil may ->onDelete('cascade') sa migration mo, automatic na ring mabubura ang mga questions nito sa DB!
        $quiz->delete();

        return redirect()->route('teacher.quizzes.index')
            ->with('message', 'Quiz deleted successfully.');
    }


    // Sa loob ng QuizCrudController.php

    public function edit(Quiz $quiz)
    {
        // I-load ang questions kasama ang quiz
        $quiz->load('questions');
        
        $lessons = Lesson::where('user_id', Auth::id())->get();

        return Inertia::render('Teacher/Quizzes/Edit', [
            'quiz' => $quiz,
            'lessons' => $lessons
        ]);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'passing_score' => 'required|integer',
            'questions' => 'required|array|min:1',
        ]);

        // 1. Update Quiz Meta
        $quiz->update([
            'title' => $request->title,
            'lesson_id' => $request->lesson_id,
            'passing_score' => $request->passing_score,
        ]);

        // 2. Delete old questions and re-insert new ones (Simplest Sync Approach)
        $quiz->questions()->delete();

        foreach ($request->questions as $q) {
            $quiz->questions()->create([
                'question_text' => $q['question_text'],
                'question_type' => $q['question_type'],
                'options' => $q['options'] ?? null,
                'correct_answer' => $q['correct_answer'],
                'points' => $q['points'] ?? 1,
                'explanation' => $q['explanation'] ?? null,
            ]);
        }

        return redirect()->route('teacher.quizzes.index')->with('message', 'Quiz updated!');
    }
}
