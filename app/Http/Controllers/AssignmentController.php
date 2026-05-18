<?php
namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AssignmentController extends Controller
{
    // 1. LISTAHAN NG MGA ASSIGNMENTS / ACTIVITIES
    public function index()
    {
        $assignments = Assignment::where('user_id', Auth::id())
            ->with('lesson')
            ->latest()
            ->get();

        return Inertia::render('Teacher/Assignments/Index', [
            'assignments' => $assignments
        ]);
    }

    // 2. FORM PARA SA PAGGAWA
    public function create()
    {
        $lessons = Lesson::whereHas('subject', function($query) {
            $query->where('user_id', Auth::id());
        })->get();

        return Inertia::render('Teacher/Assignments/Create', [
            'lessons' => $lessons
        ]);
    }

    // 3. PAG-SAVE NG BAGONG ASSIGNMENT
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'required|string',
            'max_points' => 'required|integer|min:1',
            'due_date' => 'nullable|date',
            'lesson_id' => 'nullable|exists:lessons,id',
        ]);

        Assignment::create([
            'user_id' => Auth::id(),
            'lesson_id' => $request->lesson_id,
            'title' => $request->title,
            'instructions' => $request->instructions,
            'max_points' => $request->max_points,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('teacher.assignments.index')
            ->with('message', 'Assignment/Activity created successfully!');
    }

    // 4. FORM PARA SA PAG-EDIT
    public function edit(Assignment $assignment)
    {
        if ($assignment->user_id !== Auth::id()) { abort(403); }

        $lessons = Lesson::whereHas('subject', function($query) {
            $query->where('user_id', Auth::id());
        })->get();

        return Inertia::render('Teacher/Assignments/Edit', [
            'assignment' => $assignment,
            'lessons' => $lessons
        ]);
    }

    // 5. PAG-UPDATE NG DATA
    public function update(Request $request, Assignment $assignment)
    {
        if ($assignment->user_id !== Auth::id()) { abort(403); }

        $request->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'required|string',
            'max_points' => 'required|integer|min:1',
            'due_date' => 'nullable|date',
        ]);

        $assignment->update([
            'title' => $request->title,
            'lesson_id' => $request->lesson_id,
            'instructions' => $request->instructions,
            'max_points' => $request->max_points,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('teacher.assignments.index')
            ->with('message', 'Assignment updated successfully!');
    }

    // 6. PAG-DELETE
    public function destroy(Assignment $assignment)
    {
        if ($assignment->user_id !== Auth::id()) { abort(403); }

        $assignment->delete();

        return redirect()->route('teacher.assignments.index')
            ->with('message', 'Assignment deleted.');
    }
}