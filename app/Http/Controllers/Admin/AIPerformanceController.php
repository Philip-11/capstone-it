<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class AIPerformanceController extends Controller
{
    public function generateReport(Subject $subject, User $student)
    {
        //Get data from submissions table
        $submissions = $student->submissions()
            ->whereHas('assignment', function($query) use ($subject) {
                $query->where('subject_id', $subject->id);
            })->with('assignment')->get();

        $totalAssignments = $submissions->count();
        $onTimeCount = 0;
        $lateCount = 0;
        $totalGrade = 0;

        foreach ($submissions as $sub) {
            $totalGrade += $sub->grade ?? 0;

            if ($sub->assignment && $sub->assignment->due_date){
                if ($sub->created_at->gt($sub->assignment->due_date)){
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
        $attempts = $subject->attempt()
            ->where('user_id', $student->id)->get();

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

            $rawJsonText = $result['candidates'][0]['contents']['parts'][0]['text'] ?? '{}';
            $aiOutput = json_decode($rawJsonText, true);
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
