<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = ['quiz_id', 'question_text', 'question_type', 'options', 'correct_answer', 'points', 'explanation'];

    // Automatically convert the JSON 'options' column into a PHP array
    protected $casts = [
        'options' => 'array',
    ];

    // Link back to the parent Quiz
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
