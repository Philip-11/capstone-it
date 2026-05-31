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


        return $unlockedBadges;
    }
}
