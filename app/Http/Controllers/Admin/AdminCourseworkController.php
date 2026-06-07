<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Lesson;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminCourseworkController extends Controller
{
    public function manageSubject(Subject $subject)
    {
        $subject->load(['lessons.assignments', 'lessons.quizzes', 'teacher', 'students']);

        return Inertia::render('Admin/Subjects/Manage', [
            'subject' => $subject,
        ]);
    }

    public function storeLesson(Request $request, Subject $subject)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $subject->lessons()->create($request->only('title', 'content'));

        return redirect()->back()->with('success', 'Lesson created successfully');
    }

    public function updateLesson(Request $request, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $lesson->update($request->only('title', 'content'));

        return redirect()->back()->with('success', 'Lesson updated successfully');
    }

    public function destroyLesson(Lesson $lesson)
    {
        $lesson->delete();
        
        return redirect()->back()->with('success', 'Lesson deleted successfully');
    }

    //Assignment crud
    public function storeAssignment(Request $request, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
        ]);

        $lesson->assignment()->create($request->only('title', 'due_date'));
        return redirect()->back()->with('success', 'Assignment created');
    }

    public function updateAssignment(Request $request, Assignment $assignment)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
        ]);

        $assignment->update($request->only('title', 'due date'));

        return redirect()->back()->with('success', 'Assignment updated');
    }

    public function destroyAssignment(Assignment $assignment)
    {
        $assignment->delete();
        
        return redirect()->back()->with('success', 'Assignment deleted');
    }

    public function storeQuiz(Request $request, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'time_limit' => 'nullable|integer', // sa minuto pre
        ]);

        $lesson->quizzes()->create($request->only('title', 'time_limit'));
        return redirect()->back()->with('success', 'Admin: Quiz deployed!');
    }

    public function updateQuiz(Request $request, Quiz $quiz)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'time_limit' => 'nullable|integer',
        ]);

        $quiz->update($request->only('title', 'time_limit'));
        return redirect()->back()->with('success', 'Admin: Quiz updated!');
    }

    public function destroyQuiz(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->back()->with('success', 'Admin: Quiz deleted permanently!');
    }
}
