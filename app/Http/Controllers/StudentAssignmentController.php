<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Progress;
use App\Services\GamificationService;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentAssignmentController extends Controller
{
    public function show(Assignment $assignment)
    {
        $assignment->load('lesson');

        $existingSubmission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', Auth::id())
            ->first();

        return Inertia::render('Student/Assignments/Show', [
            'assignment' => $assignment,
            'submission' => $existingSubmission,
        ]); 
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $request->validate([
            'submission_text' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,docx,doc,zip,png,jpg|max:5120',
        ]);

        $filePath = null;

        if ($request->hasFile('file')){
            $file = $request->file('file');
            $originalName = $request->file('file')->getClientOriginalName();
            $filePath = $file->storeAs('submissions', $originalName, 'public'); 
        }

        AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'user_id' => Auth::id(),
            ],
            [
                'submission_text' => $request->submission_text,
                'file_path' => $filePath ?? $request->old_file_path, //the old file stays if theres no new upload
                'submitted_at' => now(),
            ]
        );

        try {
            Progress::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'lesson_id' => $assignment->lesson_id,
                ],
                [
                    'status' => 'completed',
                    'last_accessed_at' => now(),
                ]
            );
        } catch (\Exception $e){
            dd("Database Error: " . $e->getMessage());
        }
        

        $gamificationResult = GamificationService::awardXp(Auth::id(), 50);

        if ($gamificationResult['leveled_up']){
            session()->flash('level_up', 'Congratulations! You leveled up to ' . $gamificationResult['level'] . "!");
        }

        return redirect()->route('student.subjects.show', $assignment->lesson->subject_id)
            ->with('message', 'Assignment/Activity Submitted Successfully');
    }

    public function assignments()
    {
        $user = Auth::user();
        $userId = $user->id;

        $subjectIds = $user->joinedSubjects->pluck('id')->toArray();

        $assignments = Assignment::whereHas('lesson', function($query) use ($subjectIds){
            $query->whereIn('subject_id', $subjectIds);
        })
        ->with(['lesson.subject', 'submissions' => function($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
        ->orderBy('due_date', 'asc')
        ->get()
        ->map(function ($assignment) {
            $assignment->is_submitted = $assignment->submissions->isNotEmpty();
            return $assignment;
        });

        return Inertia::render('Student/Assignments/AssignmentListView', [
            'assignments' => $assignments
        ]);
    }
}
