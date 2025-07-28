<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\CoursePeriodController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserImportController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\GradebookController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\EventOrganizerController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\EssaySubmissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Profile Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // DUPLICATION ROUTES
    Route::post('/courses/{course}/duplicate', [CourseController::class, 'duplicate'])->name('courses.duplicate');
    Route::post('/courses/{course}/lessons/{lesson}/duplicate', [LessonController::class, 'duplicate'])->name('lessons.duplicate');
    Route::post('/lessons/{lesson}/contents/{content}/duplicate', [ContentController::class, 'duplicate'])->name('contents.duplicate');

    // ✅ PERBAIKAN: Mengubah URL rute AJAX agar tidak konflik
    Route::get('/ajax/quizzes/get-full-quiz-form-partial', fn() => view('quizzes.partials.full-quiz-form')->render())->name('quiz-full-form-partial');
    Route::get('/ajax/quizzes/get-question-form-partial', fn(Illuminate\Http\Request $request) => view('quizzes.partials.question-form-fields', ['question_loop_index' => $request->query('index'), 'question' => null])->render())->name('quiz-question-partial');

    // Pengumuman
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [AnnouncementController::class, 'indexForUser'])->name('index');
        Route::get('/api/for-user', [AnnouncementController::class, 'getForUser'])->name('api.for-user');
        Route::post('/mark-as-read/{announcement}', [AnnouncementController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-as-read', [AnnouncementController::class, 'markAllAsRead'])->name('mark-all-as-read');
        Route::get('/unread-count', [AnnouncementController::class, 'getUnreadCount'])->name('unread-count');
    });

    // Rute untuk Riwayat Pengumuman
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');

    // Kursus, Pelajaran, dan Konten
    Route::resource('courses', CourseController::class);
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enrollParticipant'])->name('courses.enroll');
    Route::delete('/courses/{course}/unenroll-mass', [CourseController::class, 'unenrollParticipants'])->name('courses.unenroll_mass');
    Route::post('/courses/{course}/add-instructor', [CourseController::class, 'addInstructor'])->name('courses.addInstructor');
    Route::delete('/courses/{course}/remove-instructor', [CourseController::class, 'removeInstructor'])->name('courses.removeInstructor');

    Route::get('/courses/{course}/progress', [CourseController::class, 'showProgress'])->name('courses.progress');
    Route::get('/courses/{course}/progress/pdf', [CourseController::class, 'downloadProgressPdf'])->name('courses.progress.pdf');
    Route::get('/courses/{course}/participant/{user}/progress', [CourseController::class, 'showParticipantProgress'])->name('courses.participant.progress');

    Route::resource('courses.lessons', LessonController::class)->except(['index', 'show']);
    Route::post('lessons/update-order', [LessonController::class, 'updateOrder'])->name('lessons.update_order');
    Route::post('contents/update-order', [ContentController::class, 'updateOrder'])->name('contents.update_order');
    Route::resource('lessons.contents', ContentController::class)->except(['index', 'show']);
    Route::get('/contents/{content}', [ContentController::class, 'show'])->name('contents.show');
    Route::post('lessons/{lesson}/complete', [ProgressController::class, 'markLessonAsCompleted'])->name('lessons.complete');

    // Kuis & Esai
    Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::get('/quizzes/{quiz}/start', [QuizController::class, 'start'])->name('quizzes.start');
    Route::post('/quizzes/{quiz}/start', [QuizController::class, 'startAttempt'])->name('quizzes.start_attempt');
    Route::post('/quizzes/{quiz}/attempts/{attempt}/save-progress', [QuizController::class, 'saveProgress'])
        ->name('quizzes.save_progress')
        ->middleware('auth');
    Route::get('/quizzes/{quiz}/attempts/{attempt}/check-time', [QuizController::class, 'checkTimeRemaining'])
        ->name('quizzes.check_time')
        ->middleware('auth');
    Route::post('/quizzes/{quiz}/attempt/{attempt}/submit', [QuizController::class, 'submitAttempt'])->name('quizzes.submit_attempt');
    Route::get('/quizzes/{quiz}/attempt/{attempt}/result', [QuizController::class, 'showResult'])->name('quizzes.result');
    Route::post('/essays/{content}/submit', [EssaySubmissionController::class, 'store'])->name('essays.store');
    Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');

    // Route untuk Forum Diskusi
    Route::get('/courses/{course}/discussions', [App\Http\Controllers\DiscussionController::class, 'index'])->name('courses.discussions.index');
    Route::post('/contents/{content}/discussions', [App\Http\Controllers\DiscussionController::class, 'store'])->name('discussions.store');
    Route::post('/discussions/{discussion}/replies', [App\Http\Controllers\DiscussionController::class, 'storeReply'])->name('discussions.replies.store');

    // Route Assign EO
    Route::post('/courses/{course}/add-eo', [CourseController::class, 'addEventOrganizer'])->name('courses.addEo');
    Route::delete('/courses/{course}/remove-eo', [CourseController::class, 'removeEventOrganizer'])->name('courses.removeEo');

    // Grup Route untuk Admin, Instruktur, dan EO
    Route::middleware(['role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class)->except(['create', 'store', 'show']);
        Route::resource('roles', RoleController::class)->except(['show']);

        // [BARU] Route untuk Manajemen Pengumuman
        Route::resource('announcements', AnnouncementController::class);
        Route::patch('announcements/{announcement}/toggle-status', [AnnouncementController::class, 'toggleStatus'])
            ->name('announcements.toggle-status');

        // [PERBAIKAN] Mendefinisikan rute pengguna secara eksplisit
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Bulk Import User
        Route::get('/users/import', [UserImportController::class, 'show'])->name('users.import.show');
        Route::post('/users/import', [UserImportController::class, 'store'])->name('users.import.store');
        Route::get('/users/import/template', [UserImportController::class, 'downloadTemplate'])->name('users.import.template');

        // Pengumuman
        Route::resource('announcements', AdminAnnouncementController::class);
        Route::patch('announcements/{announcement}/toggle-status', [AdminAnnouncementController::class, 'toggleStatus'])
            ->name('announcements.toggle-status');
    });

    // Route untuk Gradebook
    Route::middleware(['permission:grade quizzes'])->group(function () {
        Route::get('/courses/{course}/gradebook', [GradebookController::class, 'index'])->name('courses.gradebook');
        Route::get('/courses/{course}/gradebook/essays/user/{user}', [GradebookController::class, 'showUserEssays'])->name('gradebook.user_essays');
        Route::post('/essay-submissions/{submission}/grade', [GradebookController::class, 'storeEssayGrade'])->name('gradebook.storeEssayGrade');
        Route::post('/courses/{course}/participant/{user}/feedback', [GradebookController::class, 'storeFeedback'])->name('gradebook.storeFeedback');
    });

    Route::get('/essay-submissions/{submission}/result', [EssaySubmissionController::class, 'showResult'])->name('essays.result');

    Route::middleware(['permission:view progress reports'])->prefix('event-organizer')->name('eo.')->group(function () {
        Route::get('/courses', [EventOrganizerController::class, 'index'])->name('courses.index');
    });

    // Export PDF
    Route::get('/courses/{course}/export-progress-pdf', [ProgressController::class, 'exportCourseProgressPdf'])
        ->name('courses.exportProgressPdf')
        ->middleware('auth');

    // Prasyarat
    Route::post('/contents/{content}/complete-and-continue', [ContentController::class, 'completeAndContinue'])->name('contents.complete_and_continue')->middleware('auth');

    // Course Period routes
    Route::resource('course-periods', CoursePeriodController::class);
    Route::get('/course-periods/create/{course}', [CoursePeriodController::class, 'create'])
        ->name('course-periods.create');

    // NEW: Chat interface routes
    Route::get('/chat', [App\Http\Controllers\Api\ChatController::class, 'webIndex'])->name('chat.index');
    Route::get('/chat/{chat}', [App\Http\Controllers\Api\ChatController::class, 'webShow'])->name('chat.show');
    Route::post('/chats/{chat}/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/chats/{chat}/typing', [MessageController::class, 'UserTyping'])->name('messages.UserTyping');

    // Chat API routes untuk WEB interface (tanpa sanctum!)
     // Chat API routes untuk WEB interface
    Route::middleware('force.json')->group(function () {
        Route::post('/chats', [App\Http\Controllers\Api\ChatController::class, 'store'])->name('chats.store');
        Route::get('/users/available', [App\Http\Controllers\Api\ChatController::class, 'availableUsers'])->name('chats.users');
        Route::get('/course-periods/available', [App\Http\Controllers\Api\ChatController::class, 'availableCoursePeriods'])->name('chats.periods');
    });
});

require __DIR__.'/auth.php';
