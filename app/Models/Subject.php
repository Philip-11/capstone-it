<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sem', 'course', 'subject_code', 'user_id'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lessons(){
        return $this->hasMany(Lesson::class);
    }

    public function students(){
        return $this->belongsToMany(User::class, 'subject_student')
            ->withTimestamps();
    }
}
