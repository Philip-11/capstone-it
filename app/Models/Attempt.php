<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attempt extends Model
{
    protected $fillable = ['user_id', 'quiz_id', 'score', 'total_questions', 'completed_at'];

    //To link back to the student
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //To link back to the quiz they took
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
