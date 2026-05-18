<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Quiz;
use App\Models\Subject;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(){
        return Inertia::render('Student/Dashboard', [
            'joinedSubjects' => Auth::user()->joinedSubjects()->with('teacher')->get(),
        ]);
    }

    public function joinSubject(Request $request){
        $request->validate([
            'subject_code' => 'required|exists:subjects,subject_code',   
        ], [
            'subject_code.exists' => 'Invalid Subject Code. Please check and try again.'
        ]);

        $subject = Subject::where('subject_code', '=', $request->subject_code)->first();

        //Attach the student to the subject
        Auth::user()->joinedSubjects()->syncWithoutDetaching([$subject->id]);

        return redirect()->back()->with('message', "Succesfully joined {$subject->name}!");
    }

    public function showSubject(Subject $subject){
        if (!$subject->students->contains(Auth::id())) {
            abort(403, "You are not enrolled in this subject");
        }

        return Inertia::render('Student/SubjectView', [
            'subject' => $subject->load(['lessons.quiz', 'teacher']),
        ]);
    }

    public function showLesson($id){

    }

    public function showQuiz(Quiz $quiz)
    {
        $quiz->load(['questions' => function($query){
            $query->select('id', 'quiz_id', 'question_text', 'question_type', 'options', 'points');
        }]);

        return Inertia::render('Student/QuizView', [
            'quiz' => $quiz
        ]);
    }

    public function strclean($string)
    {
        return strtolower(trim($string));
    }

    public function submitQuiz(Request $request, Quiz $quiz)
    {
        //'answers' is an array from vue
        $studentAnswers = $request->input('answers', []);

        $totalQuestions = $quiz->questions()->count();
        $score = 0;

        //take all the questions with answers for checking
        $questions = $quiz->questions();

        foreach ($questions as $question){
            $studentAnswer = $studentAnswers[$question->id] ?? null;
            //compare the student's answer to correct answer
            if (strclean($studentAnswer) === strclean($question->correct_answer)) {
                $score += $question->points;
            }
        }

        $attempt = Attempt::create([
            'user_id' => Auth::id(),
            'quiz_id' => $quiz->id,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'student_answers' => $studentAnswers,
            'completed_at' => now(),
        ]);

        return redirect()->route('student.subjects.show', $quiz->lesson->subject_id)
            ->with('message', 'Quiz submitted!');
    }

    public function takeQuiz($id){
        
    }
}
