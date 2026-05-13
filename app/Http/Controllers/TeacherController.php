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
        // 1. Validation - Use $validated to ensure you only use safe data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            // Make document required if you don't want empty lessons
            'document' => 'required|file|mimes:pdf,ppt,pptx|max:20480', 
        ]);

        // 2. Prepare the base data
        $data = [
            'user_id' => Auth::id(),
            'title'   => $validated['title'],
            'slug'    => Str::slug($validated['title']),
            'content' => $validated['content'],
        ];

        // 3. Handle the file storage
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            
            // This creates a path like: lessons/abc123filename.pdf
            $path = $file->store('lessons', 'public');
            
            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
        }

        // 4. Create the record
        Lesson::create($data);

        return redirect()->back()->with('message', 'Lesson created successfully!');
    }

    public function updateLesson(Request $request, Lesson $lesson)
    {
        if ($lesson->user_id !== Auth::id()){
            abort(403, 'Unauthorized Action');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $lesson->update($validated);

        return redirect()->back()->with('message', 'Lesson updated successfully');
    }

    public function destroyLesson(Lesson $lesson){
        //Make sure the right teacher is deleting their own lesson
        if ($lesson->user_id !== Auth::id()){
            abort(403, 'Unauthorized Action');
        }

        $lesson->delete();

        return redirect()->back()->with('message', 'Lesson deleted successfully');
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
