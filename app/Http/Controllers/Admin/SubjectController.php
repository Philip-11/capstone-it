<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('teacher')->orderBy('name', 'asc')->get();

        $teachers = User::where('role', 'teacher')->orderBy('name', 'asc')->get(['id', 'name']);

        return Inertia::render('Admin/Subjects/Index', [
            'subjects' => $subjects,
            'teachers' => $teachers,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'course' => 'required|string|max:255',
            'sem' => 'required|string|max:255',
            'subject_code' => 'nullable|string|max:10|unique:subjects,subject_code',
        ]);

        $code = $request->subject_code ?: strtoupper(Str::random(6));

        Subject::create([
            'name' => $request->name,
            'user_id' => $request->user_id,
            'course' => $request->course,
            'sem' => $request->sem,
            'subject_code' => strtoupper($code),
        ]);

        return redirect()->back()->with('success', 'Subject created successfully');
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'course' => 'required|string|max:255',
            'sem' => 'required|string|max:255',
            'subject_code' => 'nullable|string|max:10|unique:subjects,subject_code,' . $subject->id,
        ]);

        $code = $request->subject_code ?: $subject->subject_code;

        $subject->update([
            'name' => $request->name,
            'user_id' => $request->user_id,
            'course' => $request->course,
            'sem' => $request->sem,
            'subject_code' => strtoupper($code),
        ]);

        return redirect()->back()->with('success', 'Subject updated successfully');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->back()->with('success', 'Subject deleted successfully');
    }
}
