<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
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
        $user = Auth::user();

        $lessons = $user->lessons()->with('quizzes', 'subject')->latest()->get();
        $subjects = Subject::where('user_id', '=', $user->id)->get();

        $quizAttempts = Attempt::whereHas('quiz', function($query) use ($user){
            $query->where('user_id', $user->id);
        })
        ->with(['student', 'quiz'])
        ->latest()
        ->get();

        return Inertia::render('Teacher/Dashboard', [
            'lessons' => $lessons,
            'subjects' => $subjects,
            'quizAttempts' => $quizAttempts,
        ]);
    }

    public function indexLesson()
    {
        // 1. Kuhanin ang kasalukuyang naka-log in na teacher session
        $user = Auth::user();

        // 2. Kuhanin lahat ng lessons na pagmamay-ari ng teacher na 'to (kasama ang linked quiz metadata structural state)
        $lessons = $user->lessons()
            ->with('quizzes') // Eager load para sa 'Linked Quiz' indicator badge mo sa UI catalog list
            ->latest()
            ->get();

        // 3. Kuhanin ang active courses/subjects list ng teacher para mag-hydrate ang selection dropdown list menu
        $subjects = Subject::where('user_id', $user->id)
            ->latest()
            ->get();

        // 4. I-render ang standalone single-page workspace via Inertia
        return Inertia::render('Teacher/Lessons/Index', [
            'lessons'  => $lessons,
            'subjects' => $subjects,
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
        // 1. Hanapin ang umiiral na lesson record o mag-throw ng 404
        $lesson = Lesson::findOrFail($id);

        // 2. Validation - Sabayan natin kung ano ang rules ng storeLesson (kasama ang subject_id)
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'subject_id' => 'required|exists:subjects,id', // Idinagdag para synchronized sa store control
            'document' => 'nullable|file|mimes:pdf,ppt,pptx|max:20480', // hanggang 20MB
        ]);

        // 3. I-update ang basic meta attributes gamit ang safe validated data
        $lesson->title = $validated['title'];
        $lesson->slug = Str::slug($validated['title']);
        $lesson->content = $validated['content'];
        $lesson->subject_id = $validated['subject_id']; // Naka-sync na rin sa target subject selection

        // 4. Handle storage swapping kung may bagong file na binato si teacher
        if ($request->hasFile('document')) {
            $file = $request->file('document');

            // Proteksyon: I-delete ang lumang file sa public storage para walang ghost files na kumakain ng space
            if ($lesson->file_path && Storage::disk('public')->exists($lesson->file_path)) {
                Storage::disk('public')->delete($lesson->file_path);
            }

            // I-store ang bagong asset file
            $path = $file->store('lessons', 'public');
            
            $lesson->file_path = $path;
            $lesson->file_name = $file->getClientOriginalName();
        }

        // 5. I-save ang bawat binagong attributes sa database row
        $lesson->save();

        return redirect()->back()->with('message', 'Lesson updated successfully!');
    }

    public function destroyLesson(Lesson $lesson)
    {
        // 1. Security Gatekeeper: Siguraduhing ang mismong teacher lang na gumawa ang pwedeng mag-delete
        if ($lesson->user_id !== Auth::id()) {
            abort(403, 'Unauthorized Action. You do not own this lesson record.');
        }

        // 2. Storage Cleanup: I-verify kung talagang may file at buhay pa sa server bago i-execute ang delete command
        if ($lesson->file_path && Storage::disk('public')->exists($lesson->file_path)) {
            Storage::disk('public')->delete($lesson->file_path);
        }

        // 3. Database Purge: Burahin na ang record sa lessons table
        $lesson->delete();

        // 4. Redirect Back to Dashboard Interface
        return redirect()->back()->with('message', 'Lesson and its associated files have been deleted successfully.');
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
