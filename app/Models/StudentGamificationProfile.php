<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGamificationProfile extends Model
{
    protected $fillable = ['user_id', 'xp', 'level', 'streak', 'last_activity_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
