<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    //Teacher relationships

    //Teacher can create many lessons
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    //Teacher can create many quizzes
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    //Student relationships
    
    //Student can have many attempts on a quiz
    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }

    //Student can have many progress logs
    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }

    //Student can have many AI reports
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    //For teacher: to see the lessons the teacher made
    public function managedSubjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function joinedSubjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_student');
    }

    public function gamificationProfile()
    {
        return $this->hasOne(StudentGamificationProfile::class);
    }


}
