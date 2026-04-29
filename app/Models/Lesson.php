<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lesson extends Model
{
    protected $fillable = ['user_id', 'title', 'slug', 'description', 'content', 'video_url', 'order', 'is_published'];

    // The Teacher who created the lesson
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // A lesson can have one quiz attached to it
    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    // Tracking how many students have interacted with this lesson
    public function progressLogs(): HasMany
    {
        return $this->hasMany(Progress::class);
    }
}