<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\GradebookController;
use App\Http\Controllers\EssaySubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', \App\Http\Controllers\DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/quizzes/get-full-quiz-form-partial', function () {
        return view('quizzes.partials.full-quiz-form')->render();
    })->name('quiz-full-form-partial');

    Route::get('/quizzes/get-question-form-partial', function (Illuminate\Http\Request $request) {
        $index = $request->query('index');
        return view('quizzes.partials.question-form-fields', [
            'question_loop_index' => $index,
            'question' => null
        ])->render();
    })->name('quiz-question-partial');

    Route::post('essays/{content}/submit', [EssaySubmissionController::class, 'store'])->name('essays.store');
    Route::get('/courses/{course}/progress', [App\Http\Controllers\CourseController::class, 'showProgress'])->name('courses.progress');
    Route::get('/courses/{course}/participant/{user}/progress', [App\Http\Controllers\CourseController::class, 'showParticipantProgress'])->name('courses.participant.progress');

    // Grup route untuk Super Admin
    Route::middleware(['role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'store', 'show']);
        Route::resource('roles', RoleController::class)->except(['show']);
    });

    // Grup route untuk Manajemen Kursus
    Route::middleware(['can:manage own courses'])->group(function () {
        Route::get('/courses/{course}/gradebook', [GradebookController::class, 'index'])->name('courses.gradebook');
        Route::get('/quiz-attempts/{attempt}/review', [GradebookController::class, 'review'])->name('gradebook.review');
        Route::get('/courses/{course}/participant/{user}/feedback', [GradebookController::class, 'feedback'])->name('gradebook.feedback');
        Route::post('/courses/{course}/participant/{user}/feedback', [GradebookController::class, 'storeFeedback'])->name('gradebook.storeFeedback');
        Route::post('/essay-submissions/{submission}/grade', [App\Http\Controllers\GradebookController::class, 'storeEssayGrade'])->name('gradebook.storeEssayGrade');
        Route::get('/courses/{course}/gradebook/essays/user/{user}', [App\Http\Controllers\GradebookController::class, 'showUserEssays'])->name('gradebook.user_essays');
    });

    Route::resource('courses', CourseController::class);
    Route::resource('lessons', LessonController::class);
    Route::resource('courses.lessons', LessonController::class)->except(['index', 'show']);
    Route::resource('lessons.contents', ContentController::class)->except(['index', 'show']);
    Route::post('courses/{course}/enroll', [CourseController::class, 'enrollParticipant'])->name('courses.enroll');
    Route::delete('courses/{course}/unenroll-mass', [CourseController::class, 'unenrollParticipants'])->name('courses.unenroll_mass');

    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/lessons/{lesson}/contents/{content}', [ContentController::class, 'show'])->name('contents.show');

    Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{quiz}/start', [QuizController::class, 'startAttempt'])->name('quizzes.start_attempt');
    Route::post('/quizzes/{quiz}/attempt/{attempt}/submit', [QuizController::class, 'submitAttempt'])->name('quizzes.submit_attempt');
    Route::get('/quizzes/{quiz}/attempt/{attempt}/result', [QuizController::class, 'showResult'])->name('quizzes.result');
    Route::get('/quizzes/{quiz}/start', [QuizController::class, 'start'])->name('quizzes.start');
    
    Route::get('/contents/{content}', [\App\Http\Controllers\ContentController::class, 'show'])->name('contents.show');
    Route::resource('contents', \App\Http\Controllers\ContentController::class)->except(['show']);
    Route::post('lessons/{lesson}/complete', [App\Http\Controllers\ProgressController::class, 'markLessonAsCompleted'])->name('lessons.complete');
    Route::post('essay-submissions', [App\Http\Controllers\EssaySubmissionController::class, 'store'])->name('essay.submit');
});

require __DIR__.'/auth.php';
