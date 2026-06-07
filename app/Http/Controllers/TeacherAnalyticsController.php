<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Attempt;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class TeacherAnalyticsController extends Controller
{
    public function showStudentReport(Subject $subject, User $student)
    {
        $user = Auth::user();

        if ($subject->user_id !== $user->id){
            abort(403, 'Unauthorized Access');
        }

        $lessonIds = $subject->lessons()->pluck('id');
        $assignmentIds = Assignment::where('lesson_id', $lessonIds)->pluck('id');

        $submissions = $student->submissions()
            ->whereIn('assignment_id', $assignmentIds)
            ->with('assignment')
            ->get();

        $totalAssignments = $submissions->count();
        $onTimeCount = 0;
        $lateCount = 0;
        $totalGrade = 0;

        foreach ($submissions as $sub){
            $totalGrade += $sub->grade ?? 0;
            if ($sub->assignment && $sub->assignment->due_date){
                $dueDate = Carbon::parse($sub->assignment->due_date)->setTimezone('Asia/Manila');
                $submittedDate = Carbon::parse($sub->created_at)->setTimezone('Asia/Manila');

                if ($submittedDate->gt($dueDate)){
                    $lateCount++;
                } else {
                    $onTimeCount++;
                }
            } else {
                $onTimeCount++;
            }
        }

        $averageGrade = $totalAssignments > 0 ? round($totalGrade / $totalAssignments, 2) : 0;

        $quizIds = Quiz::whereIn('lesson_id', $lessonIds)->pluck('id');
        $attempts = Attempt::whereIn('quiz_id', $quizIds)
            ->where('user_id', $student->id)
            ->get();

        $quizAttemptsCount = $attempts->count();

        $analyticsData = [
            'total_submissions' => $totalAssignments,
            'on_time_count' => $onTimeCount,
            'late_count' => $lateCount,
            'average_grade' => $averageGrade,
            'quiz_attempts_count' => $quizAttemptsCount
        ];

        $prompt = "You are an expert academic mentor and classroom instructor. Analyze the following local database performance metrics for my student in the course '{$subject->name}':
        - Total Assignments Submitted: {$totalAssignments}
        - On-Time Submissions: {$onTimeCount}
        - Late Submissions: {$lateCount}
        - Average Coursework Grade: {$averageGrade}%
        - Total Quiz Assessment Attempts: {$quizAttemptsCount}

        Provide a tactical pedagogical diagnostic report in strict JSON format (No markdown backticks). The keys must be exactly:
        'standing' (brief status, e.g., 'Exhibiting Cramming Tendencies'), 
        'summary' (a supportive narrative on their learning behavior), 
        'strengths' (classroom competencies noticed), 
        'weaknesses' (vulnerabilities in this specific subject), 
        'recommendations' (actionable teaching interventions or modern study tips the teacher can give to the student).";

        $apiKey = env('GEMINI_API_KEY');
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}", [
            'contents' => 
            [
                'parts' => [
                    ['text' => $prompt,]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
            ],
        ]);

        $aiOutput = null;
        if ($response->successful()) {
            $result = $response->json();
            $rawJsonText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            $cleanJsonText = trim(preg_replace('/^```json\s*|```\s*$/i', '', $rawJsonText));
            $aiOutput = json_decode($cleanJsonText, true);
        }

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($aiOutput)) {
            $aiOutput = [
                'standing' => 'Evaluation Format Sync Pending',
                'summary' => 'System successfully acquired academic scores but encountered formatting limits.',
                'strengths' => ['Metrics safely recorded'], 'weaknesses' => ['LLM stream delay'], 'recommendations' => ['Refresh the dashboard analytics layer.']
            ];
        }

        return Inertia::render('Teacher/AIStudentReport', [
            'subject' => $subject,
            'student' => $student,
            'analytics' => $analyticsData,
            'ai_report' => $aiOutput,
        ]);
    }
}
