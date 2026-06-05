<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StudentQuizController extends Controller
{
    public function quizzes()
    {
        $user = Auth::user();
        $userId = $user->id;

        // 1. Kunin ang subject IDs ng mga sinalihan niya
        $subjectIds = $user->joinedSubjects->pluck('id')->toArray();

        // 2. Hilahin lahat ng quizzes sa mga subjects na 'yon
        $quizzes = Quiz::whereHas('lesson', function($query) use ($subjectIds) {
                $query->whereIn('subject_id', $subjectIds);
            })
            ->with(['lesson.subject', 'attempts' => function($query) use ($userId) {
                // I-load ang attempt ng kasalukuyang user para malaman kung nasagutan na
                $query->where('user_id', $userId);
            }])
            ->get()
            ->map(function ($quiz) {
                // Maglagay ng custom attribute para sa Vue conditional rendering
                // Kung may record sa attempts table, itinuturing na nakuha na niya ito
                $quiz->is_submitted = $quiz->attempts->isNotEmpty();
                return $quiz;
            });

        return Inertia::render('Student/QuizListView', [
            'quizzes' => $quizzes
        ]);
    }
}
