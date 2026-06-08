<?php

namespace App\Services;

use App\Models\AssignmentSubmission;
use App\Models\Badge;
use App\Models\User;

class BadgeCheckerService
{
    public static function checkAll(User $user, $currentStreak)
    {
        $unlockedBadges = [];

        $hasFirstBlood = $user->badges()->where('badge_code', 'first_submission')->exists();
        if (!$hasFirstBlood){
            $submissionCourt = AssignmentSubmission::where('user_id', $user->id)->count();
            if ($submissionCourt >= 1){
                $badge = Badge::where('badge_code', 'first_submission')->first();
                if ($badge){
                    $user->badges()->attach($badge->id);
                    $unlockedBadges[] = $badge->name;
                }
            }
        }

        $hasStreak3 = $user->badges()->where('badge_code', 'streak_3')->exists();
        if(!$hasStreak3 && $currentStreak >= 3){
            $badge = Badge::where('badge_code', 'streak_3')->first();
            if ($badge){
                $user->badges()->attach($badge->id);
                $unlockedBadges[] = $badge->name;
            }
        }

        $hasFlawless = $user->badges()->where('badge_code', 'flawless_execution')->exists();
        if (!$hasFlawless) {
            // Check if there is an attempt where the score equals the total points of the quiz
            $hasPerfectQuiz = \App\Models\Attempt::where('user_id', $user->id)
                ->whereHas('quiz', function($query) {
                    $query->whereColumn('attempts.score', '=', 'quizzes.total_points');
                })->exists();

            if ($hasPerfectQuiz) {
                $badge = Badge::where('badge_code', 'flawless_execution')->first();
                if ($badge) {
                    $user->badges()->attach($badge->id);
                    $unlockedBadges[] = $badge->name;
                }
            }
        }

        $hasExplorer = $user->badges()->where('badge_code', 'curriculum_explorer')->exists();
        if (!$hasExplorer) {

            $enrolledSubjectIds = \DB::table('subject_student')
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->pluck('subject_id');

            if ($enrolledSubjectIds->isNotEmpty()) {
        
                $totalAssignmentsCount = \App\Models\Assignment::whereHas('lesson', function($query) use ($enrolledSubjectIds) {
                    $query->whereIn('subject_id', $enrolledSubjectIds);
                })->count();

                
                $studentSubmissionsCount = \App\Models\AssignmentSubmission::where('user_id', $user->id)
                    ->whereHas('assignment.lesson', function($query) use ($enrolledSubjectIds) {
                        $query->whereIn('subject_id', $enrolledSubjectIds);
                    })
                    ->distinct('assignment_id')
                    ->count();

                
                if ($totalAssignmentsCount > 0 && $studentSubmissionsCount >= $totalAssignmentsCount) {
                    $badge = Badge::where('badge_code', 'curriculum_explorer')->first();
                    if ($badge) {
                        $user->badges()->attach($badge->id);
                        $unlockedBadges[] = $badge->name;
                    }
                }
            }
        }

        return $unlockedBadges;
    }
}
