// routes/web.php

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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


    // ROUTE UNTUK MANAJEMEN KURSUS, PELAJARAN, KONTEN, dan KUIS
    Route::middleware(['can:manage-courses'])->group(function () { // Ini adalah grup middleware yang sudah ada
        Route::resource('courses', CourseController::class);

        // Nested resources untuk pelajaran di bawah kursus
        Route::resource('courses.lessons', LessonController::class)->except(['index', 'show']);
        // Nested resources untuk konten di bawah pelajaran
        Route::resource('lessons.contents', ContentController::class)->except(['index', 'show']);

        // Mengubah route enroll untuk menerima banyak user_ids
        Route::post('courses/{course}/enroll', [CourseController::class, 'enrollParticipant'])->name('courses.enroll');
        // Menambahkan route untuk unenroll massal
        Route::delete('courses/{course}/unenroll-mass', [CourseController::class, 'unenrollParticipants'])->name('courses.unenroll_mass');

        // Route unenroll per user (lama) bisa dihapus jika tidak diperlukan lagi
        // Route::delete('courses/{course}/unenroll/{user}', [CourseController::class, 'unenrollParticipant'])->name('courses.unenroll');
    });

    // Rute untuk peserta melihat kursus, pelajaran, konten (sekarang juga kuis untuk peserta)
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/lessons/{lesson}/contents/{content}', [ContentController::class, 'show'])->name('contents.show');

    // Rute terkait kuis untuk peserta (ini tidak perlu middleware 'can:manage-courses')
    Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{quiz}/start', [QuizController::class, 'startAttempt'])->name('quizzes.start_attempt');
    Route::post('/quizzes/{quiz}/attempt/{attempt}/submit', [QuizController::class, 'submitAttempt'])->name('quizzes.submit_attempt');
    Route::get('/quizzes/{quiz}/attempt/{attempt}/result', [QuizController::class, 'showResult'])->name('quizzes.result');

    Route::post('contents/{content}/complete', [App\Http\Controllers\ProgressController::class, 'markContentAsCompleted'])->name('contents.complete');
    Route::post('lessons/{lesson}/complete', [App\Http\Controllers\ProgressController::class, 'markLessonAsCompleted'])->name('lessons.complete');
});

require __DIR__.'/auth.php';