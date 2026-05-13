<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;

class TeacherController extends Controller
{

    public function index()
    {
        return Inertia::render('Teacher/Dashboard', [
            'lessons' => Auth::user()->lessons()->with('quiz', 'subject')->latest()->get(),
            'subjects' => Subject::where('user_id', '=', Auth::user()->id)->get(),
        ]);
    }

    public function storeLesson(Request $request)
    {
        // 1. Validation - Use $validated to ensure you only use safe data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            // Make document required if you don't want empty lessons
            'document' => 'nullable|file|mimes:pdf,ppt,pptx|max:20480', 
        ]);

        // 2. Prepare the base data
        $data = [
            'user_id' => Auth::id(),
            'title'   => $validated['title'],
            'slug'    => Str::slug($validated['title']),
            'content' => $validated['content'],
            'subject_id' => $request->subject_id, 
            'file_path' => $path ?? null,
            'file_name' => $originalName ?? null,
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

        return redirect()->back()->with('message', 'Lesson uploaded to subject!');
    }

    public function updateLesson(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,ppt,pptx|max:20480', 
        ]);

        $lesson->title = $validated['title'];
        $lesson->content = $validated['content'];
        $lesson->slug = Str::slug($validated['title']);

        if ($request->hasFile('document')) {
            // Optional: Delete old file so you don't waste space
            if ($lesson->file_path) {
                Storage::disk('public')->delete($lesson->file_path);
            }

            $path = $request->file('document')->store('lessons', 'public');
            $lesson->file_path = $path;
            $lesson->file_name = $request->file('document')->getClientOriginalName();
        }

        $lesson->save();

        return redirect()->back()->with('message', 'Lesson updated!');
    }

    public function destroyLesson(Lesson $lesson){
        //Make sure the right teacher is deleting their own lesson
        if ($lesson->user_id !== Auth::id()){
            abort(403, 'Unauthorized Action');
        }

        if ($lesson->file_path){
            Storage::disk('public')->delete($lesson->file_path);
        }

        $lesson->delete();

        return redirect()->back()->with('message', 'Lesson deleted successfully');
    }

    public function downloadLesson(Lesson $lesson){
        if ($lesson->user_id !== Auth::id()){
            abort(403);
        }

        // $path = storage_path('app/public' . $lesson->file_path);

        // dd($path);

        // if (!file_exists($path)){
        //     return redirect()->back()->withErrors(['error' => 'File no longer exists on the server']);
        // }

        // return response()->download($path, $lesson->file_name);

        if (!Storage::disk('public')->exists($lesson->file_path)){
            dd("File NOT FOUND in storage/app/public/" . $lesson->file_path);
        }

        return Storage::disk('public')->download($lesson->file_path, $lesson->file_name);
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

    public function indexSubjects()
    {
        $subjects = Subject::where('user_id', '=', Auth::id())->latest()->get();

        return Inertia::render('Teacher/Subjects', [
            'subjects' => $subjects
        ]);
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Subject::create([
            'name' => $request->name,
            'subject_code' => strtoupper(Str::random(6)),
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('message', 'Subject created successfully');
    }

}
