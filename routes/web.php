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
use App\Http\Controllers\Admin\CertificateTemplateController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\GradebookController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\EventOrganizerController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\EssaySubmissionController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\EssayQuestionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/certificates/verify/{code}', [CertificateController::class, 'verify'])->name('certificates.verify');

// Ini adalah route untuk download dari halaman verifikasi
Route::get('/certificates/download/{code}', [CertificateController::class, 'publicDownload'])->name('certificates.public-download');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Kredensial Sertifikat
    Route::get('/certificate/{code}', [CertificateController::class, 'show'])->name('certificate.show');

    // Upload image teks editor
    Route::post('/images/upload', [ImageUploadController::class, 'store'])->name('images.upload');

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

    Route::post('/contents/{content}/essay-questions', [EssayQuestionController::class, 'store'])
        ->name('essay.questions.store');

    Route::delete('/essay-questions/{question}', [EssayQuestionController::class, 'destroy'])
        ->name('essay.questions.destroy');

    Route::put('/contents/{content}/essay-questions/order', [EssayQuestionController::class, 'updateOrder'])
        ->name('essay.questions.update-order');

    Route::put('/essay-questions/{question}', [EssayQuestionController::class, 'update'])
        ->name('essay.questions.update');

    // Route untuk Forum Diskusi
    Route::get('/courses/{course}/discussions', [App\Http\Controllers\DiscussionController::class, 'index'])->name('courses.discussions.index');
    Route::post('/contents/{content}/discussions', [App\Http\Controllers\DiscussionController::class, 'store'])->name('discussions.store');
    Route::post('/discussions/{discussion}/replies', [App\Http\Controllers\DiscussionController::class, 'storeReply'])->name('discussions.replies.store');

    // Route Assign EO
    Route::post('/courses/{course}/add-eo', [CourseController::class, 'addEventOrganizer'])->name('courses.addEo');
    Route::delete('/courses/{course}/remove-eo', [CourseController::class, 'removeEventOrganizer'])->name('courses.removeEo');

    // Course Period routes
    Route::resource('course-periods', CoursePeriodController::class);
    Route::get('/course-periods/create/{course}', [CoursePeriodController::class, 'create'])
        ->name('course-periods.create');
    Route::post('/course-periods/create/{course}', [CoursePeriodController::class, 'store'])
        ->name('course-periods.store');

    // ============================================================================
    // 🔥 UPDATED CHAT ROUTES - Layout Terpadu dengan Sidebar + Main Chat Area
    // ============================================================================

    // Main chat interface - unified layout (index dengan sidebar + chat area)
    Route::get('/chat', [App\Http\Controllers\Api\ChatController::class, 'webIndex'])
        ->name('chat.index');

    // Individual chat view - AJAX endpoint untuk load chat ke main area
    Route::get('/chat/{chat}', [App\Http\Controllers\Api\ChatController::class, 'webShow'])
        ->name('chat.show');

    // Chat search endpoint untuk search functionality di sidebar
    Route::get('/chat/search', [App\Http\Controllers\Api\ChatController::class, 'search'])
        ->name('chat.search');

    // Message routes - untuk send/receive messages
    Route::post('/chats/{chat}/messages', [MessageController::class, 'store'])
        ->name('messages.store');

    Route::post('/chats/{chat}/typing', [MessageController::class, 'UserTyping'])
        ->name('messages.UserTyping');

    // Chat API routes untuk WEB interface (JSON responses) - tetap menggunakan force.json middleware
    Route::middleware('force.json')->group(function () {
        Route::post('/chats', [App\Http\Controllers\Api\ChatController::class, 'store'])
            ->name('chats.store');

        Route::get('/users/available', [App\Http\Controllers\Api\ChatController::class, 'availableUsers'])
            ->name('chats.users');

        Route::get('/course-periods/available', [App\Http\Controllers\Api\ChatController::class, 'availableCoursePeriods'])
            ->name('chats.periods');
    });

    // ============================================================================
    // END CHAT ROUTES
    // ============================================================================

    // Export PDF
    Route::get('/courses/{course}/export-progress-pdf', [ProgressController::class, 'exportCourseProgressPdf'])
        ->name('courses.exportProgressPdf')
        ->middleware('auth');

    // Prasyarat
    Route::post('/contents/{content}/complete-and-continue', [ContentController::class, 'completeAndContinue'])->name('contents.complete_and_continue')->middleware('auth');

    // Grup Route untuk Admin, Instruktur, dan EO
    Route::middleware(['role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class)->except(['create', 'store', 'show']);
        Route::resource('roles', RoleController::class)->except(['show']);

        // Sertifikat
        Route::get('certificate-templates/create/enhanced', [CertificateTemplateController::class, 'createEnhanced'])->name('certificate-templates.create-enhanced');
        Route::get('certificate-templates/{certificateTemplate}/edit/enhanced', [CertificateTemplateController::class, 'editEnhanced'])->name('certificate-templates.edit-enhanced');
        Route::resource('certificate-templates', CertificateTemplateController::class);

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
        Route::get('/users/{user}/reset-password', [UserController::class, 'resetPasswordForm'])->name('users.reset-password-form');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
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

        Route::post('/essay-submissions/{submission}/grade-multi', [GradebookController::class, 'storeMultiQuestionGrade'])
            ->name('gradebook.store-multi-grade');

        // ✅ TAMBAHAN: Route untuk overall grading (dengan scoring)
        Route::post('/essay-submissions/{submission}/grade-overall', [GradebookController::class, 'storeOverallGrade'])
            ->name('gradebook.store-overall-grade');

        // ✅ TAMBAHAN: Route untuk overall feedback (tanpa scoring)
        Route::post('/essay-submissions/{submission}/feedback-overall', [GradebookController::class, 'storeOverallFeedback'])
            ->name('gradebook.store-overall-feedback');

        Route::get('/essay-submissions/{submission}/detail', [GradebookController::class, 'showEssayDetail'])
            ->name('gradebook.essay-detail');
        
        Route::post('/essay-submissions/{submission}/grade-overall', [GradebookController::class, 'storeOverallGrade'])
            ->name('gradebook.store-overall-grade');
        Route::post('/essay-submissions/{submission}/feedback-overall', [GradebookController::class, 'storeOverallFeedback'])
            ->name('gradebook.store-overall-feedback');

        Route::post('/essay-submissions/{submission}/feedback-only', [GradebookController::class, 'storeEssayFeedbackOnly'])
            ->name('gradebook.storeEssayFeedbackOnly');
    });

    Route::get('/essay-submissions/{submission}/result', [EssaySubmissionController::class, 'showResult'])->name('essays.result');

    Route::middleware(['permission:view progress reports'])->prefix('event-organizer')->name('eo.')->group(function () {
        Route::get('/courses', [EventOrganizerController::class, 'index'])->name('courses.index');
    });

    Route::get('/certificates/create/{course}', [CertificateController::class, 'create'])->name('certificates.create');
    Route::post('/certificates/generate', [CertificateController::class, 'generate'])->name('certificates.generate');
    Route::post('/certificates/store', [CertificateController::class, 'store'])->name('certificates.store');

    Route::get('/certificates', [CertificateController::class, 'index'])
        ->name('certificates.index');

    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])
        ->name('certificates.download');

    // Course-specific certificate management (for instructors/organizers)
    Route::get('/courses/{course}/certificates', [CertificateController::class, 'courseIndex'])
        ->name('courses.certificates.index');

    Route::post('/courses/{course}/users/{user}/certificates/generate', [CertificateController::class, 'generate'])
        ->name('courses.certificates.generate');

    Route::post('/courses/{course}/certificates/bulk-generate', [CertificateController::class, 'bulkGenerate'])
        ->name('courses.certificates.bulk-generate');

    Route::post('/certificates/{certificate}/regenerate', [CertificateController::class, 'regenerate'])
        ->name('certificates.regenerate');
    Route::get('/certificates/{certificate}', [CertificateController::class, 'destroy'])
        ->name('certificates.show');

    Route::delete('/certificates/{certificate}', [CertificateController::class, 'destroy'])
        ->name('certificates.destroy');

    // Certificate Management Routes
    Route::middleware(['permission:view progress reports'])->prefix('certificate-management')->name('certificate-management.')->group(function () {
        Route::get('/', [CertificateController::class, 'managementIndex'])->name('index');
        Route::get('/analytics', [CertificateController::class, 'analytics'])->name('analytics');
        Route::get('/by-course/{course}', [CertificateController::class, 'byCourse'])->name('by-course');
        Route::post('/bulk-action', [CertificateController::class, 'bulkAction'])->name('bulk-action');
        Route::post('/{certificate}/update-template', [CertificateController::class, 'updateTemplate'])->name('update-template');
    });

    // Instructor Analytics Routes
    Route::middleware(['permission:view progress reports'])->prefix('instructor-analytics')->name('instructor-analytics.')->group(function () {
        Route::get('/', [GradebookController::class, 'instructorAnalytics'])->name('index');
        Route::get('/instructor/{user}', [GradebookController::class, 'instructorDetail'])->name('detail');
        Route::get('/compare', [GradebookController::class, 'instructorCompare'])->name('compare');
    });
});

// ============================================================================
// 🔥 TRUE API ROUTES - untuk mobile apps, external integrations, etc (dengan sanctum)
// ============================================================================
Route::middleware('auth:sanctum')->prefix('api')->group(function () {
    // Chat API routes untuk mobile/external apps
    Route::prefix('chats')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ChatController::class, 'index']);
        Route::post('/', [App\Http\Controllers\Api\ChatController::class, 'store']);
        Route::get('/{chat}', [App\Http\Controllers\Api\ChatController::class, 'show']);
        Route::get('/{chat}/messages', [App\Http\Controllers\Api\MessageController::class, 'index']);
        Route::post('/{chat}/messages', [App\Http\Controllers\Api\MessageController::class, 'store']);
        Route::post('/{chat}/typing', [App\Http\Controllers\Api\MessageController::class, 'UserTyping']);
        Route::get('/search', [App\Http\Controllers\Api\ChatController::class, 'search']);
    });

    // Helper routes untuk mobile/external apps
    Route::get('/users/available', [App\Http\Controllers\Api\ChatController::class, 'availableUsers']);
    Route::get('/course-periods/available', [App\Http\Controllers\Api\ChatController::class, 'availableCoursePeriods']);
});

require __DIR__ . '/auth.php';
