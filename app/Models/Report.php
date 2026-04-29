<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = ['user_id', 'ai_summary', 'detailed_feedback', 'performance_level', 'suggested_lessons'];

    protected $casts = ['suggested_lessons' => 'array'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
