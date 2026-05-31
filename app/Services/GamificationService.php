<?php

namespace App\Services;

use App\Models\StudentGamificationProfile;
use Carbon\Carbon;

class GamificationService
{
    public static function awardXp($userId, $xpAmount)
    {
        $profile = StudentGamificationProfile::firstOrCreate(
            ['user_id' => $userId],
            ['xp' => 0, 'level' => 1, 'streak' => 0, 'last_activity_date' => null]
        );

        $profile->xp += $xpAmount;

        $calculatedLevel = floor($profile->xp / 200) + 1;

        $leveledUp = false;
        if ($calculatedLevel > $profile->level){
            $profile->level = $calculatedLevel;
            $leveledUp = true;
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        if ($profile->last_activity_date == $yesterday->toDateString()){
            $profile->streak += 1; 
        } elseif ($profile->last_activity_date != $today->toDateString()){
            $profile->streak = 1;  
        }

        $profile->last_activity_date = $today->toDateString();
        $profile->save();

        $unlockedBadges = [];
        $user = $profile->user;

        if ($user){
            $unlockedBadges = BadgeCheckerService::checkAll($user, $profile->streak);
        }

        return [
            'level' => $profile->level,
            'streak' => $profile->streak,
            'leveled_up' => $leveledUp,
            'unlocked_badges' => $unlockedBadges,
        ];
    }
}
