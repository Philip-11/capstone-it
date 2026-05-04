<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);


Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsTeacher::class])->group(function () {

    //Teacher dashboard
    Route::get('/teacher/lessons', [TeacherController::class, 'index'])->name('teacher.lessons.name');

    Route::post('/teacher/lessons', [TeacherController::class, 'storeLesson'])->name('teacher.lessons.store');

    Route::post('/teacher/lesson/{lesson}/quiz', [TeacherController::class, 'storeQuiz'])->name('teacher.quiz.store');

});