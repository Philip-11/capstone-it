<?php

use App\Http\Controllers\Admin\AdminCourseworkController;
use App\Http\Controllers\Admin\AIPerformanceController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AIChatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\StudentAssignmentController;
use App\Http\Controllers\StudentQuizController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('auth/login');
});
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
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
    Route::put('/teacher/submissions/{submission}/grade', [AssignmentController::class, 'gradeSubmission'])->name('teacher.submissions.grade');
});

//Student Route
Route::middleware(['auth'])->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'index'])->name('student.dashboard');
    Route::post('/student/join-subject', [StudentController::class, 'joinSubject'])->name('student.join');

    Route::get('/student/subjects/{subject}', [StudentController::class, 'showSubject'])->name('student.subjects.show');

    Route::get('/student/quiz/{quiz}', [StudentController::class, 'showQuiz'])->name('student.quiz.show');
    Route::get('/student/quizzes', [StudentQuizController::class, 'quizzes'])->name('student.quizzes.index');
    Route::post('/student/quiz/{quiz}/submit', [StudentController::class, 'submitQuiz'])->name('student.quiz.submit');

    Route::get('/student/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('student.assignments.show');
    Route::get('/student/assignments/', [StudentAssignmentController::class, 'assignments'])->name('student.assignments.index');
    Route::post('/student/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('student.assignments.submit');

    //AI Route
    Route::get('/teacher/ai-chat/history', [AIChatController::class, 'getChatHistory'])->name('teacher.ai-chat.history');
    Route::post('/teacher/ai-chat', [AIChatController::class, 'handleChat'])->name('teacher.ai-chat');
});

Route::middleware(['auth'])->group(function () {
    // Admin Routes
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/admin/subjects', [SubjectController::class, 'index'])->name('admin.subjects.index');
    Route::post('/admin/subjects', [SubjectController::class, 'store'])->name('admin.subjects.store');
    Route::put('/admin/subjects/{subject}', [SubjectController::class, 'update'])->name('admin.subjects.update');
    Route::delete('/admin/subjects/{subject}', [SubjectController::class, 'destroy'])->name('admin.subjects.destroy');

    // Admin Override Management Routes
    Route::get('/admin/subjects/{subject}/manage', [AdminCourseworkController::class, 'manageSubject'])->name('admin.subjects.manage');
    
    // Lessons CRUD ng Admin
    Route::post('/admin/subjects/{subject}/lessons', [AdminCourseworkController::class, 'storeLesson'])->name('admin.lessons.store');
    Route::put('/admin/lessons/{lesson}', [AdminCourseworkController::class, 'updateLesson'])->name('admin.lessons.update');
    Route::delete('/admin/lessons/{lesson}', [AdminCourseworkController::class, 'destroyLesson'])->name('admin.lessons.destroy');

    //AI Report
    Route::get('/admin/subjects/{subject}/students/{student}/ai-report', [AIPerformanceController::class, 'generateReport'])->name('admin.subjects.student.ai-report');
});