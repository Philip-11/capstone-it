<?php

use App\Http\Controllers\AIChatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\StudentAssignmentController;
use Illuminate\Support\Facades\Route;


Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);


Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsTeacher::class])->group(function () {

    //Subject routes
    Route::get('/teacher/subjects', [TeacherController::class, 'indexSubjects'])->name('teacher.subjects.index');
    Route::post('/teacher/subjects', [TeacherController::class, 'storeSubject'])->name('teacher.subjects.store');

    //Teacher dashboard
    Route::get('/teacher/dashboard', [TeacherController::class, 'index'])->name('teacher.lessons.name');

    Route::get('/teacher/lessons', [TeacherController::class, 'indexLesson'])->name('teacher.lessons.index');
    Route::post('/teacher/lessons', [TeacherController::class, 'storeLesson'])->name('teacher.lessons.store');

    Route::post('/teacher/lessons/{lesson}/quiz', [TeacherController::class, 'storeQuiz'])->name('teacher.quiz.store');

    Route::put('/teacher/lessons/{lesson}', [TeacherController::class, 'updateLesson'])->name('teacher.lessons.update');

    Route::delete('/teacher/lessons/{lesson}', [TeacherController::class, 'destroyLesson'])->name('teacher.lessons.destroy');

    Route::get('/lessons/{lesson}/download', [TeacherController::class, 'downloadLesson'])->name('teacher.lessons.download');

    Route::get('/teacher/quizzes', [QuizController::class, 'index'])->name('teacher.quizzes.index');
    Route::get('/teacher/quizzes/create', [QuizController::class, 'create'])->name('teacher.quizzes.create');
    Route::post('/teacher/quizzes', [QuizController::class, 'store'])->name('teacher.quizzes.store');
    Route::get('/teacher/quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('teacher.quizzes.edit');
    Route::put('/teacher/quizzes/{quiz}', [QuizController::class, 'update'])->name('teacher.quizzes.update');
    Route::delete('/teacher/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('teacher.quizzes.destroy');

    Route::get('/teacher/assignments', [AssignmentController::class, 'index'])->name('teacher.assignments.index');
    Route::get('/teacher/assignments/create', [AssignmentController::class, 'create'])->name('teacher.assignments.create');
    Route::post('/teacher/assignments', [AssignmentController::class, 'store'])->name('teacher.assignments.store');
    Route::get('/teacher/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('teacher.assignments.edit');
    Route::put('/teacher/assignments/{assignment}', [AssignmentController::class, 'update'])->name('teacher.assignments.update');
    Route::delete('/teacher/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('teacher.assignments.destroy');
});

//Student Route
Route::middleware(['auth'])->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'index'])->name('student.dashboard');
    Route::post('/student/join-subject', [StudentController::class, 'joinSubject'])->name('student.join');

    Route::get('/student/subjects/{subject}', [StudentController::class, 'showSubject'])->name('student.subjects.show');

    Route::get('/student/quiz/{quiz}', [StudentController::class, 'showQuiz'])->name('student.quiz.show');
    Route::post('/student/quiz/{quiz}/submit', [StudentController::class, 'submitQuiz'])->name('student.quiz.submit');

    Route::get('/student/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('student.assignments.show');
    Route::post('/student/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('student.assignments.submit');

    //AI Route
    Route::get('/teacher/ai-chat/history', [AIChatController::class, 'getChatHistory'])->name('teacher.ai-chat.history');
    Route::post('/teacher/ai-chat', [AIChatController::class, 'handleChat'])->name('teacher.ai-chat');
});