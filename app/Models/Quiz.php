<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = ['user_id', 'lesson_id', 'title', 'passing_score'];

    // The Teacher who created it
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // The lesson this quiz belongs to
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    // The questions inside this quiz
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    // All student attempts for this quiz
    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }
}
