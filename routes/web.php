<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\CourseClassController;
use App\Http\Controllers\TokenEnrollmentController;
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
use App\Http\Controllers\FileControlController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AttendanceController;

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
    Route::post('/images/upload', [ImageUploadController::class, 'store'])
        ->name('images.upload')
        ->middleware('permission:upload files');

    // File Control routes
    Route::get('/file-control', [FileControlController::class, 'index'])
        ->name('file-control.index')
        ->middleware('permission:view files');
    Route::post('/file-control/upload', [FileControlController::class, 'upload'])
        ->name('file-control.upload')
        ->middleware('permission:upload files');
    Route::post('/file-control/delete', [FileControlController::class, 'delete'])
        ->name('file-control.delete')
        ->middleware('permission:delete files');
    Route::get('/file-control/files', [FileControlController::class, 'getFiles'])
        ->name('file-control.files')
        ->middleware('permission:view files');

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity-logs.index')
        ->middleware('permission:view activity logs');
    Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show'])
        ->name('activity-logs.show')
        ->middleware('permission:view activity logs');
    Route::post('/activity-logs/clear', [ActivityLogController::class, 'clear'])
        ->name('activity-logs.clear')
        ->middleware('permission:clear activity logs');
    Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])
        ->name('activity-logs.export')
        ->middleware('permission:export activity logs');

    // Attendance Management
    // IMPORTANT: Specific routes BEFORE generic routes to avoid conflicts
    Route::get('/attendance/content/{content}/export', [AttendanceController::class, 'export'])
        ->name('attendance.export')
        ->middleware('permission:export attendance');
    Route::get('/attendance/content/{content}', [AttendanceController::class, 'index'])
        ->name('attendance.index')
        ->middleware('permission:view attendance');
    Route::post('/attendance/content/{content}/mark', [AttendanceController::class, 'mark'])
        ->name('attendance.mark')
        ->middleware('permission:mark attendance');
    Route::post('/attendance/content/{content}/bulk-mark', [AttendanceController::class, 'bulkMark'])
        ->name('attendance.bulk-mark')
        ->middleware('permission:bulk mark attendance');
    Route::put('/attendance/record/{attendance}', [AttendanceController::class, 'update'])
        ->name('attendance.update')
        ->middleware('permission:update attendance');
    Route::delete('/attendance/record/{attendance}', [AttendanceController::class, 'destroy'])
        ->name('attendance.destroy')
        ->middleware('permission:delete attendance');
    Route::get('/course/{course}/attendance-report', [AttendanceController::class, 'courseReport'])
        ->name('attendance.course-report')
        ->middleware('permission:view attendance reports');

    // Profile Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // DUPLICATION ROUTES
    Route::post('/courses/{course}/duplicate', [CourseController::class, 'duplicate'])
        ->name('courses.duplicate')
        ->middleware('permission:duplicate courses');
    Route::post('/courses/{course}/lessons/{lesson}/duplicate', [LessonController::class, 'duplicate'])
        ->name('lessons.duplicate')
        ->middleware('permission:duplicate lessons');
    Route::post('/lessons/{lesson}/contents/{content}/duplicate', [ContentController::class, 'duplicate'])
        ->name('contents.duplicate')
        ->middleware('permission:duplicate contents');

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
    Route::resource('courses', CourseController::class)->middleware('permission:view courses|manage all courses');
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enrollParticipant'])->name('courses.enroll')->middleware('permission:add class participants');
    Route::delete('/courses/{course}/unenroll-mass', [CourseController::class, 'unenrollParticipants'])->name('courses.unenroll_mass')->middleware('permission:remove class participants');
    Route::post('/courses/{course}/add-instructor', [CourseController::class, 'addInstructor'])->name('courses.addInstructor')->middleware('permission:assign instructors');
    Route::delete('/courses/{course}/remove-instructor', [CourseController::class, 'removeInstructor'])->name('courses.removeInstructor')->middleware('permission:assign instructors');

    // Course Token Management
    Route::get('/courses/{course}/tokens', [CourseController::class, 'tokens'])->name('courses.tokens')->middleware('permission:manage course tokens');
    Route::post('/courses/{course}/token/generate', [CourseController::class, 'generateToken'])->name('courses.token.generate')->middleware('permission:manage course tokens');
    Route::post('/courses/{course}/token/regenerate', [CourseController::class, 'regenerateToken'])->name('courses.token.regenerate')->middleware('permission:manage course tokens');
    Route::post('/courses/{course}/token/toggle', [CourseController::class, 'toggleToken'])->name('courses.token.toggle')->middleware('permission:manage course tokens');

    Route::get('/courses/{course}/progress', [CourseController::class, 'showProgress'])->name('courses.progress');
    Route::get('/courses/{course}/progress/pdf', [CourseController::class, 'downloadProgressPdf'])->name('courses.progress.pdf');
    Route::get('/courses/{course}/participant/{user}/progress', [CourseController::class, 'showParticipantProgress'])->name('courses.participant.progress');

    Route::resource('courses.lessons', LessonController::class)->except(['index', 'show'])->middleware('permission:manage own courses');
    Route::post('lessons/update-order', [LessonController::class, 'updateOrder'])->name('lessons.update_order')->middleware('permission:update lessons');
    Route::post('contents/update-order', [ContentController::class, 'updateOrder'])->name('contents.update_order')->middleware('permission:update contents');
    Route::resource('lessons.contents', ContentController::class)->except(['index', 'show'])->middleware('permission:manage own courses');
    Route::get('/contents/{content}', [ContentController::class, 'show'])->name('contents.show');
    Route::post('lessons/{lesson}/complete', [ProgressController::class, 'markLessonAsCompleted'])->name('lessons.complete');

    // Kuis & Esai
    // IMPORTANT: Specific routes must come BEFORE dynamic routes with parameters
    Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index')->middleware('permission:view quizzes');
    Route::get('/quizzes/download-template', [QuizController::class, 'downloadTemplate'])->name('quizzes.download-template');
    Route::get('/quizzes/import/form', [QuizController::class, 'showImport'])->name('quizzes.import-form')->middleware('permission:manage own courses');
    Route::post('/quizzes/import', [QuizController::class, 'import'])->name('quizzes.import')->middleware('permission:manage own courses');

    // Dynamic routes with parameters come AFTER specific routes
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
    Route::get('/quizzes/{quiz}/leaderboard', [QuizController::class, 'leaderboard'])->name('quizzes.leaderboard');

    Route::post('/essays/{content}/submit', [EssaySubmissionController::class, 'store'])->name('essays.store');
    Route::post('/essays/{content}/autosave', [EssaySubmissionController::class, 'autosave'])->name('essays.autosave');
    Route::get('/essays/{content}/drafts', [EssaySubmissionController::class, 'getDrafts'])->name('essays.get_drafts');

    Route::post('/contents/{content}/essay-questions', [EssayQuestionController::class, 'store'])
        ->name('essay.questions.store')
        ->middleware('permission:manage essay questions');

    Route::delete('/essay-questions/{question}', [EssayQuestionController::class, 'destroy'])
        ->name('essay.questions.destroy')
        ->middleware('permission:manage essay questions');

    Route::put('/contents/{content}/essay-questions/order', [EssayQuestionController::class, 'updateOrder'])
        ->name('essay.questions.update-order')
        ->middleware('permission:manage essay questions');

    Route::put('/essay-questions/{question}', [EssayQuestionController::class, 'update'])
        ->name('essay.questions.update')
        ->middleware('permission:manage essay questions');

    // Route untuk Forum Diskusi
    Route::get('/courses/{course}/discussions', [App\Http\Controllers\DiscussionController::class, 'index'])->name('courses.discussions.index')->middleware('permission:view discussions');
    Route::post('/contents/{content}/discussions', [App\Http\Controllers\DiscussionController::class, 'store'])->name('discussions.store')->middleware('permission:create discussions');
    Route::post('/discussions/{discussion}/replies', [App\Http\Controllers\DiscussionController::class, 'storeReply'])->name('discussions.replies.store')->middleware('permission:reply discussions');

    // Route Assign EO
    Route::post('/courses/{course}/add-eo', [CourseController::class, 'addEventOrganizer'])->name('courses.addEo')->middleware('permission:assign event organizers');
    Route::delete('/courses/{course}/remove-eo', [CourseController::class, 'removeEventOrganizer'])->name('courses.removeEo')->middleware('permission:assign event organizers');

    // Token Enrollment routes
    Route::post('/enroll', [TokenEnrollmentController::class, 'enroll'])
        ->name('enroll')
        ->middleware('permission:enroll courses');
    Route::post('/enroll/course', [TokenEnrollmentController::class, 'enrollCourse'])
        ->name('enroll.course')
        ->middleware('permission:enroll courses');
    Route::post('/enroll/class', [TokenEnrollmentController::class, 'enrollClass'])
        ->name('enroll.class')
        ->middleware('permission:enroll courses');

    // Course Class routes
    Route::get('/courses/{course}/periods', [CourseClassController::class, 'index'])
        ->name('course-periods.index')
        ->middleware('permission:view classes');
    Route::get('/courses/{course}/periods/create', [CourseClassController::class, 'create'])
        ->name('course-periods.create')
        ->middleware('permission:create classes');
    Route::post('/courses/{course}/periods', [CourseClassController::class, 'store'])
        ->name('course-periods.store')
        ->middleware('permission:create classes');
    Route::get('/courses/{course}/periods/{period}', [CourseClassController::class, 'show'])
        ->name('course-periods.show')
        ->middleware('permission:view classes');
    Route::get('/courses/{course}/periods/{period}/edit', [CourseClassController::class, 'edit'])
        ->name('course-periods.edit')
        ->middleware('permission:update classes');
    Route::put('/courses/{course}/periods/{period}', [CourseClassController::class, 'update'])
        ->name('course-periods.update')
        ->middleware('permission:update classes');
    Route::delete('/courses/{course}/periods/{period}', [CourseClassController::class, 'destroy'])
        ->name('course-periods.destroy')
        ->middleware('permission:delete classes');
    Route::post('/courses/{course}/periods/{period}/duplicate', [CourseClassController::class, 'duplicate'])
        ->name('course-periods.duplicate')
        ->middleware('permission:duplicate classes');

    // Class management routes
    Route::get('/courses/{course}/periods/{period}/manage', [CourseClassController::class, 'manage'])
        ->name('course-periods.manage')
        ->middleware('permission:update classes');
    Route::post('/courses/{course}/periods/{period}/instructors', [CourseClassController::class, 'addInstructor'])
        ->name('course-periods.add-instructor')
        ->middleware('permission:assign class instructors');
    Route::delete('/courses/{course}/periods/{period}/instructors/{user}', [CourseClassController::class, 'removeInstructor'])
        ->name('course-periods.remove-instructor')
        ->middleware('permission:remove class instructors');
    Route::post('/courses/{course}/periods/{period}/participants', [CourseClassController::class, 'addParticipant'])
        ->name('course-periods.add-participant')
        ->middleware('permission:add class participants');
    Route::delete('/courses/{course}/periods/{period}/participants/{user}', [CourseClassController::class, 'removeParticipant'])
        ->name('course-periods.remove-participant')
        ->middleware('permission:remove class participants');
    Route::delete('/courses/{course}/periods/{period}/participants', [CourseClassController::class, 'bulkRemoveParticipants'])
        ->name('course-periods.bulk-remove-participants')
        ->middleware('permission:remove class participants');
    Route::post('/courses/{course}/periods/{period}/enroll', [CourseClassController::class, 'enroll'])
        ->name('course-periods.enroll')
        ->middleware('permission:enroll class participants');

    // Class Token Management
    Route::post('/courses/{course}/periods/{period}/token/generate', [CourseClassController::class, 'generateToken'])->name('course-periods.token.generate')->middleware('permission:manage class tokens');
    Route::post('/courses/{course}/periods/{period}/token/regenerate', [CourseClassController::class, 'regenerateToken'])->name('course-periods.token.regenerate')->middleware('permission:manage class tokens');
    Route::post('/courses/{course}/periods/{period}/token/toggle', [CourseClassController::class, 'toggleToken'])->name('course-periods.token.toggle')->middleware('permission:manage class tokens');

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

        Route::get('/course-classes/available', [App\Http\Controllers\Api\ChatController::class, 'availableCourseClasses'])
            ->name('chats.periods');

        // Chat participants management
        Route::post('/chats/{chat}/participants', [App\Http\Controllers\Api\ChatController::class, 'addParticipants'])
            ->name('chats.participants.add');
        Route::delete('/chats/{chat}/participants/{user}', [App\Http\Controllers\Api\ChatController::class, 'removeParticipant'])
            ->name('chats.participants.remove');
    });

    // ============================================================================
    // END CHAT ROUTES
    // ============================================================================

    // Export PDF
    Route::get('/courses/{course}/export-progress-pdf', [ProgressController::class, 'exportCourseProgressPdf'])
        ->name('courses.exportProgressPdf')
        ->middleware('permission:generate reports');

    // Participant: My Scores page (per course)
    Route::get('/courses/{course}/my-scores', [ProgressController::class, 'myScores'])
        ->name('courses.my-scores');

    // Course Scores (Admin/Instruktur/EO) - view-only participant scores per course
    Route::get('/courses/{course}/scores', [CourseController::class, 'showScores'])
        ->name('courses.scores')
        ->middleware('permission:view progress reports');

    // Prasyarat
    Route::post('/contents/{content}/complete-and-continue', [ContentController::class, 'completeAndContinue'])->name('contents.complete_and_continue')->middleware('auth');

    // Grup Route untuk Admin, Instruktur, dan EO
    Route::middleware(['permission:manage users|manage roles|view certificate templates|view activity logs|view announcements|view certificate analytics|view certificate management'])->prefix('admin')->name('admin.')->group(function () {
        // Add explicit permission middleware so these can be opened to admin-like roles later safely
        Route::resource('roles', RoleController::class)->except(['show'])->middleware('permission:manage roles');

        // Participants
        Route::get('/participants', [\App\Http\Controllers\Admin\ParticipantController::class, 'index'])->name('participants.index')->middleware('permission:manage users');
        Route::get('/participants/analytics', [\App\Http\Controllers\Admin\ParticipantController::class, 'analytics'])->name('participants.analytics')->middleware('permission:manage users');
        Route::get('/participants/{user}', [\App\Http\Controllers\Admin\ParticipantController::class, 'show'])->name('participants.show')->middleware('permission:manage users');

        // Sertifikat
        Route::get('certificate-templates/create/enhanced', [CertificateTemplateController::class, 'createEnhanced'])->name('certificate-templates.create-enhanced')->middleware('permission:create certificate templates');
        Route::get('certificate-templates/create/advanced', [CertificateTemplateController::class, 'createAdvanced'])->name('certificate-templates.create-advanced')->middleware('permission:create certificate templates');
        Route::get('certificate-templates/{certificateTemplate}/edit/enhanced', [CertificateTemplateController::class, 'editEnhanced'])->name('certificate-templates.edit-enhanced')->middleware('permission:update certificate templates');
        Route::get('certificate-templates/{certificateTemplate}/edit/advanced', [CertificateTemplateController::class, 'editAdvanced'])->name('certificate-templates.edit-advanced')->middleware('permission:update certificate templates');
        Route::get('certificate-templates/{certificateTemplate}/preview', [CertificateTemplateController::class, 'preview'])->name('certificate-templates.preview')->middleware('permission:preview certificate templates');
        Route::post('certificate-templates/{certificateTemplate}/preview', [CertificateTemplateController::class, 'generatePreview'])->name('certificate-templates.generate-preview')->middleware('permission:preview certificate templates');
        Route::post('certificate-templates/{certificateTemplate}/duplicate', [CertificateTemplateController::class, 'duplicate'])->name('certificate-templates.duplicate')->middleware('permission:duplicate certificate templates');
        Route::resource('certificate-templates', CertificateTemplateController::class)->middleware('permission:view certificate templates|create certificate templates|update certificate templates|delete certificate templates|duplicate certificate templates|preview certificate templates');

        // Tools (Admin Utilities)
        Route::get('/tools', [\App\Http\Controllers\Admin\ToolsController::class, 'index'])
            ->name('tools.index')
            ->middleware('permission:manage users|manage roles');
        Route::post('/tools/permissions/refresh', [\App\Http\Controllers\Admin\ToolsController::class, 'refreshPermissionCache'])
            ->name('tools.permissions.refresh')
            ->middleware('permission:manage users|manage roles');
        Route::get('/tools/roles/export', [\App\Http\Controllers\Admin\ToolsController::class, 'exportRoleMatrix'])
            ->name('tools.roles.export')
            ->middleware('permission:manage users|manage roles');

        // [BARU] Route untuk Manajemen Pengumuman (use admin controller)

        // [PERBAIKAN] Mendefinisikan rute pengguna secara eksplisit (avoid duplicating resource routes)
        Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('permission:manage users');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:manage users');
        Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('permission:manage users');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:manage users');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:manage users');
        Route::get('/users/{user}/reset-password', [UserController::class, 'resetPasswordForm'])->name('users.reset-password-form')->middleware('permission:manage users');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password')->middleware('permission:manage users');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:manage users');

        // Bulk Import User
        Route::get('/users/import', [UserImportController::class, 'show'])->name('users.import.show');
        Route::post('/users/import', [UserImportController::class, 'store'])->name('users.import.store');
        Route::get('/users/import/template', [UserImportController::class, 'downloadTemplate'])->name('users.import.template');

        // Pengumuman
        Route::resource('announcements', AdminAnnouncementController::class)->middleware('permission:view announcements|create announcements|update announcements|delete announcements|publish announcements');
        Route::patch('announcements/{announcement}/toggle-status', [AdminAnnouncementController::class, 'toggleStatus'])
            ->name('announcements.toggle-status')
            ->middleware('permission:publish announcements|update announcements');

        // Automatic Grading Completion
        Route::get('/auto-grade', [\App\Http\Controllers\Admin\AutoGradeController::class, 'index'])->name('auto-grade.index')->middleware('permission:grade essays|grade quizzes');
        Route::post('/auto-grade/complete', [\App\Http\Controllers\Admin\AutoGradeController::class, 'processAutoGrade'])->name('auto-grade.complete')->middleware('permission:grade essays|grade quizzes');
        Route::post('/auto-grade/complete-all', [\App\Http\Controllers\Admin\AutoGradeController::class, 'processAutoGradeAll'])->name('auto-grade.complete-all')->middleware('permission:grade essays|grade quizzes');

        // Force Complete (mark all contents completed)
        Route::get('/force-complete', [\App\Http\Controllers\Admin\ForceCompleteController::class, 'index'])->name('force-complete.index');
        Route::post('/force-complete/complete', [\App\Http\Controllers\Admin\ForceCompleteController::class, 'processForceComplete'])->name('force-complete.complete');
        Route::post('/force-complete/complete-all', [\App\Http\Controllers\Admin\ForceCompleteController::class, 'processForceCompleteAll'])->name('force-complete.complete-all');
        Route::post('/force-complete/bulk', [\App\Http\Controllers\Admin\ForceCompleteController::class, 'bulkForceComplete'])->name('force-complete.bulk');
        Route::post('/force-complete/bulk-certificates', [\App\Http\Controllers\Admin\ForceCompleteController::class, 'bulkGenerateCertificates'])->name('force-complete.bulk-certificates');
    });

    // Route untuk Gradebook
    // GET index terbuka untuk pengguna yang berhak lihat progres (policy di controller),
    // sedangkan aksi grading tetap dibatasi dengan permission 'grade quizzes'.
    Route::get('/courses/{course}/gradebook', [GradebookController::class, 'index'])->name('courses.gradebook');
    Route::middleware(['permission:grade quizzes'])->group(function () {
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

    Route::middleware(['permission:view courses|view progress reports'])->prefix('event-organizer')->name('eo.')->group(function () {
        Route::get('/courses', [EventOrganizerController::class, 'index'])->name('courses.index');
    });

    // Self-service certificate generation for participants (no permission check, logic handled in controller)
    Route::get('/my-certificates/generate/{course}', [CertificateController::class, 'create'])->name('my-certificates.generate');

    // Admin/Instructor certificate management routes (requires permission)
    Route::get('/certificates/create/{course}', [CertificateController::class, 'create'])->name('certificates.create')->middleware('permission:issue certificates');
    Route::post('/certificates/generate', [CertificateController::class, 'generate'])->name('certificates.generate')->middleware('permission:issue certificates');
    Route::post('/certificates/store', [CertificateController::class, 'store'])->name('certificates.store')->middleware('permission:issue certificates');

    Route::get('/certificates', [CertificateController::class, 'index'])
        ->name('certificates.index')
        ->middleware('permission:view certificates');

    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])
        ->name('certificates.download')
        ->middleware('permission:download certificates');

    // Course-specific certificate management (for instructors/organizers)
    Route::get('/courses/{course}/certificates', [CertificateController::class, 'courseIndex'])
        ->name('courses.certificates.index')
        ->middleware('permission:view certificates');

    Route::post('/courses/{course}/users/{user}/certificates/generate', [CertificateController::class, 'generate'])
        ->name('courses.certificates.generate')
        ->middleware('permission:issue certificates');

    Route::post('/courses/{course}/certificates/bulk-generate', [CertificateController::class, 'bulkGenerate'])
        ->name('courses.certificates.bulk-generate')
        ->middleware('permission:bulk issue certificates');

    Route::post('/certificates/{certificate}/regenerate', [CertificateController::class, 'regenerate'])
        ->name('certificates.regenerate')
        ->middleware('permission:regenerate certificates');
    Route::get('/certificates/{certificate}', [CertificateController::class, 'destroy'])
        ->name('certificates.show')
        ->middleware('permission:view certificates');

    Route::delete('/certificates/{certificate}', [CertificateController::class, 'destroy'])
        ->name('certificates.destroy')
        ->middleware('permission:delete certificates');

    // Certificate Management Routes
    Route::middleware(['permission:view certificate management|view progress reports'])->prefix('certificate-management')->name('certificate-management.')->group(function () {
        Route::get('/', [CertificateController::class, 'managementIndex'])->name('index');
        Route::get('/analytics', [CertificateController::class, 'analytics'])->name('analytics')->middleware('permission:view certificate analytics|view progress reports');
        Route::get('/by-course/{course}', [CertificateController::class, 'byCourse'])->name('by-course')->middleware('permission:view certificate management|view progress reports');
        Route::post('/bulk-action', [CertificateController::class, 'bulkAction'])->name('bulk-action')->middleware('permission:bulk issue certificates');
        Route::post('/{certificate}/update-template', [CertificateController::class, 'updateTemplate'])->name('update-template')->middleware('permission:update certificate template');

        // Bulk download all certificates (async)
        Route::post('/download-all', [CertificateController::class, 'downloadAll'])->name('download-all')->middleware('permission:view certificate management|view progress reports');
        Route::get('/download-status/{batchId}', [CertificateController::class, 'downloadStatus'])->name('download-status')->middleware('permission:view certificate management|view progress reports');
        Route::get('/download-zip/{batchId}', [CertificateController::class, 'downloadZip'])->name('download-zip')->middleware('permission:view certificate management|view progress reports');
    });

    // Instructor Analytics Routes
    Route::middleware(['permission:view instructor analytics|view progress reports'])->prefix('instructor-analytics')->name('instructor-analytics.')->group(function () {
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
    Route::get('/course-classes/available', [App\Http\Controllers\Api\ChatController::class, 'availableCourseClasses']);
});

require __DIR__ . '/auth.php';
        // Tools (Admin Utilities)
        Route::get('/tools', [\App\Http\Controllers\Admin\ToolsController::class, 'index'])
            ->name('tools.index')
            ->middleware('permission:manage users|manage roles');
        Route::post('/tools/permissions/refresh', [\App\Http\Controllers\Admin\ToolsController::class, 'refreshPermissionCache'])
            ->name('tools.permissions.refresh')
            ->middleware('permission:manage users|manage roles');
        Route::get('/tools/roles/export', [\App\Http\Controllers\Admin\ToolsController::class, 'exportRoleMatrix'])
            ->name('tools.roles.export')
            ->middleware('permission:manage users|manage roles');
