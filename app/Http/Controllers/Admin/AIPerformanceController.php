<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Attempt;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class AIPerformanceController extends Controller
{
    public function generateReport(Subject $subject, User $student)
    {
        //Get data from submissions table
        $lessonIds = $subject->lessons()->pluck('id');
        $assignmentIds = Assignment::whereIn('lesson_id', $lessonIds)->pluck('id');

        $submissions = $student->submissions()
            ->whereIn('assignment_id', $assignmentIds)
            ->with('assignment')
            ->get();

        $totalAssignments = $submissions->count();
        $onTimeCount = 0;
        $lateCount = 0;
        $totalGrade = 0;

        foreach ($submissions as $sub) {
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

        //Get data from attempts table
        $quizIds = Quiz::whereIn('lesson_id', $lessonIds)->pluck('id');

        $attempts = Attempt::whereIn('quiz_id', $quizIds)
            ->where('user_id', $student->id)
            ->get();

        $totalAttempts = $attempts->count();
        $averageQuizScore = $attempts->avg('score') ?? 0;
        $totalQuizItems = $attempts->avg('total_questions') ?? 0;

        $totalMinutesSpent = 0;
        foreach ($attempts as $attempt){
            $totalMinutesSpent += $attempt->created_at->diffInMinutes($attempt->updated_at);
        }
        $averageMinuteSpent = $totalAttempts > 0 ? round($totalMinutesSpent / $totalAttempts, 1) : 0;

        $analyticsData = [
            'student_name' => $student->name,
            'subject_name' => $subject->name,
            'quizzes_summary' => [
                'total_quizzes_taken' => $totalAttempts,
                'average_score_points' => $averageQuizScore . 'over ' . $totalQuizItems,
                'averate_time_spent' => $averageMinuteSpent . ' minutes per attempt',
            ],
            'assignments_summary' => [
                'total_submitted' => $totalAssignments,
                'on_time' => $onTimeCount,
                'late' => $lateCount,
                'average_grade_percentage' => $averageGrade . '%'
            ]
        ];

        $prompt = "You are an expert AI Academic Analytics Assistant. Analyze the following student performance data package and provide a comprehensive structured report.

        DATA PACKAGE:
        " . json_encode($analyticsData, JSON_PRETTY_PRINT) . "

        CRITICAL OUTPUT REQUIREMENTS:
        Your response must be returned strictly as a JSON object with the following fields:
        - 'standing': A short evaluation phrase (e.g., 'Excellent Progress', 'Needs Attention', etc.)
        - 'summary': A 3-sentence executive summary of their technical academic status.
        - 'strengths': An array of 2 bullet points detailing where the student excels based on data.
        - 'weaknesses': An array of 2 bullet points detailing where the student struggles or needs monitoring.
        - 'recommendations': An array of 3 realistic, actionable study tips or adjustments for this student.

        Do not output any markdown formatting, backticks (```json), or extra text outside the JSON object.";

        $apiKey = env('GEMINI_API_KEY');
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json'
            ]
        ]);

        $aiOutput = null;
        if ($response->successful()){
            $result = $response->json();

            $rawJsonText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

            $cleanJsonText = preg_replace('/^```json\s*/i', '', $rawJsonText); // Tanggalin ang ```json sa simula
            $cleanJsonText = preg_replace('/```\s*$/', '', $cleanJsonText);   // Tanggalin ang ``` sa dulo
            $cleanJsonText = trim($cleanJsonText);

            $aiOutput = json_decode($cleanJsonText, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($aiOutput)) {
                $aiOutput = [
                    'standing' => 'Analysis Generated with Formatting Issues',
                    'summary' => 'The system successfully read the student data, but the AI core engine returned an unparseable data packet format. Raw response: ' . substr($cleanJsonText, 0, 150) . '...',
                    'strengths' => ['Data integrity active', 'Database links verified'],
                    'weaknesses' => ['LLM response syntax anomaly'],
                    'recommendations' => ['Try running the analysis feedback query loops again.', 'Check internet synchronization parameters.']
                ];
            }
        } else {
            $aiOutput = [
                'standing' => 'Analysis Unavailable',
                'summary' => 'The AI analytics endpoint failed to generate metrics. Please double-check your Gemini API connection configuration.',
                'strengths' => ['Data processing active'],
                'weaknesses' => ['External API connectivity issue'],
                'recommendations' => ['Check your server internet access.', 'Verify GEMINI_API_KEY inside your .env file.']
            ];
        }

        return Inertia::render('Admin/Subjects/AIReport', [
            'subject' => $subject,
            'student' => $student,
            'analytics' => $analyticsData,
            'ai_report' => $aiOutput,
        ]);
    }
}
