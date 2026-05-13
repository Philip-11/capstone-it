<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;


Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);


Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsTeacher::class])->group(function () {

    //Teacher dashboard
    Route::get('/teacher/lessons', [TeacherController::class, 'index'])->name('teacher.lessons.name');

    Route::post('/teacher/lessons', [TeacherController::class, 'storeLesson'])->name('teacher.lessons.store');

    Route::post('/teacher/lessons/{lesson}/quiz', [TeacherController::class, 'storeQuiz'])->name('teacher.quiz.store');

    Route::put('/teacher/lessons/{lesson}', [TeacherController::class, 'updateLesson'])->name('teacher.lessons.update');

    Route::delete('/teacher/lessons/{lesson}', [TeacherController::class, 'destroyLesson'])->name('teacher.lessons.destroy');

    Route::get('/lessons/{lesson}/download', [TeacherController::class, 'downloadLesson'])->name('teacher.lessons.download');
});