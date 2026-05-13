<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;


Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);


Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsTeacher::class])->group(function () {

    //Subject routes
    Route::get('/teacher/subjects', [TeacherController::class, 'indexSubjects'])->name('teacher.subjects.index');
    Route::post('/teacher/subjects', [TeacherController::class, 'storeSubject'])->name('teacher.subjects.store');

    //Teacher dashboard
    Route::get('/teacher/lessons', [TeacherController::class, 'index'])->name('teacher.lessons.name');

    Route::post('/teacher/lessons', [TeacherController::class, 'storeLesson'])->name('teacher.lessons.store');

    Route::post('/teacher/lessons/{lesson}/quiz', [TeacherController::class, 'storeQuiz'])->name('teacher.quiz.store');

    Route::put('/teacher/lessons/{lesson}', [TeacherController::class, 'updateLesson'])->name('teacher.lessons.update');

    Route::delete('/teacher/lessons/{lesson}', [TeacherController::class, 'destroyLesson'])->name('teacher.lessons.destroy');

    Route::get('/lessons/{lesson}/download', [TeacherController::class, 'downloadLesson'])->name('teacher.lessons.download');
});

//Student Route
Route::middleware(['auth'])->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'index'])->name('student.dashboard');
    Route::post('/student/join-subject', [StudentController::class, 'joinSubject'])->name('student.join');

    Route::get('/student/subjects/{subject}', [StudentController::class, 'showSubject'])->name('student.subjects.show');
});