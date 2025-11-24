<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\User;
use App\Models\Course;
use App\Models\Certificate;
use App\Models\EssayQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\CertificateController;
use Carbon\Carbon;

class ContentController extends Controller
{
    public function show(Content $content)
    {
        $user = Auth::user();
        $course = $content->lesson->course;

        // Otorisasi dasar: pastikan pengguna terdaftar di kursus
        $this->authorize('view', $course);

        $orderedContents = $this->getOrderedContents($course);
        $unlockedContents = $this->getUnlockedContents($user, $orderedContents);

        if (
            !$unlockedContents->contains('id', $content->id)
            && !($user->can('update contents') || $user->can('manage own courses'))
        ) {
            return redirect()->back()->with('error', 'Anda harus menyelesaikan materi sebelumnya terlebih dahulu.');
        }

        // TAMBAH load untuk essayQuestions
        $content->load('lesson.course', 'discussions.user', 'discussions.replies.user', 'quiz.questions.options', 'essayQuestions', 'images', 'documents');

        if ($user->can('update contents') || $user->can('manage own courses')) {
            $unlockedContents = $orderedContents;
        }

        $hasPassedQuizBefore = false;
        if ($content->type === 'quiz' && $content->quiz) {
            if ($user) {
                $hasPassedQuizBefore = $user->quizAttempts()
                    ->where('quiz_id', $content->quiz->id)
                    ->where('passed', true)
                    ->exists();
            }
        }

        // ✅ FIX: Cek attendance status untuk content ini
        $attendanceStatus = null;
        $canComplete = true; // Default bisa complete

        if ($content->attendance_required) {
            $attendance = $user->attendances()
                ->where('content_id', $content->id)
                ->first();

            if (!$attendance) {
                $attendanceStatus = 'not_marked';
                $canComplete = false;
            } elseif (!in_array($attendance->status, ['present', 'excused'])) {
                $attendanceStatus = 'absent';
                $canComplete = false;
            } elseif ($content->min_attendance_minutes &&
                      $attendance->duration_minutes < $content->min_attendance_minutes) {
                $attendanceStatus = 'insufficient_duration';
                $canComplete = false;
            } else {
                $attendanceStatus = 'ok';
                $canComplete = true;
            }
        }

        return view('contents.show', compact('content', 'course', 'unlockedContents', 'hasPassedQuizBefore', 'orderedContents', 'attendanceStatus', 'canComplete'));
    }

    /**
     * ✅ TAMBAHAN: Method untuk menandai konten selesai dan lanjut ke berikutnya
     */
    public function completeAndContinue(Content $content)
    {
        $user = Auth::user();
        $lesson = $content->lesson;
        $course = $lesson->course;

        // ✅ FIX: Cek apakah content memerlukan attendance
        if ($content->attendance_required) {
            $attendance = $user->attendances()
                ->where('content_id', $content->id)
                ->first();

            // Jika belum ada attendance atau belum hadir
            if (!$attendance || !in_array($attendance->status, ['present', 'excused'])) {
                return redirect()->route('contents.show', $content->id)
                    ->with('warning', 'Anda perlu melakukan absensi terlebih dahulu sebelum dapat melanjutkan ke konten berikutnya.');
            }

            // Cek minimum duration jika diperlukan
            if ($content->min_attendance_minutes &&
                $attendance->duration_minutes < $content->min_attendance_minutes) {
                return redirect()->route('contents.show', $content->id)
                    ->with('warning', 'Anda perlu mengikuti konten ini minimal ' . $content->min_attendance_minutes . ' menit sebelum dapat melanjutkan.');
            }
        }

        // Tandai konten saat ini sebagai selesai
        $user->completedContents()->syncWithoutDetaching([
            $content->id => ['completed' => true, 'completed_at' => now()]
        ]);

        // ✅ FIX: Cari next content dengan urutan yang benar ACROSS LESSONS
        $orderedContents = $this->getOrderedContents($course);

        $currentIndex = $orderedContents->search(function ($item) use ($content) {
            return (int)$item->id === (int)$content->id;
        });

        // ✅ FIX: Ambil content berikutnya dari SELURUH course, bukan cuma lesson
        $nextContent = ($currentIndex !== false && $currentIndex < $orderedContents->count() - 1)
            ? $orderedContents->get($currentIndex + 1)
            : null;

        if ($nextContent) {
            // ✅ Ada content berikutnya - lanjut ke sana
            return redirect()->route('contents.show', ['content' => $nextContent->id])
                ->with('success', 'Lanjut ke konten berikutnya!');
        }

        // ✅ FIX: Ini adalah content TERAKHIR - cek apakah semua sudah selesai
        $allCompleted = $this->checkIfAllCourseContentCompleted($user, $course);

        if ($allCompleted) {
            // ✅ SEMUA SELESAI - generate certificate & redirect ke dashboard
            $this->checkAndGenerateCertificate($course, $user);

            $user->courses()->updateExistingPivot($course->id, ['completed_at' => now()]);

            return redirect()->route('dashboard')->with([
                'success' => '🎉 Selamat! Anda telah menyelesaikan kursus "' . $course->title . '" dengan sukses!',
                'course_completed' => true,
                'completed_course' => $course->title
            ]);
        }

        // ✅ FIX: Masih ada content yang belum selesai - redirect ke course overview
        return redirect()->route('courses.show', $course->id)
            ->with('success', 'Konten ini telah selesai. Silakan lanjutkan konten lainnya.');
    }

    private function checkIfAllCourseContentCompleted($user, $course)
    {
        $orderedContents = $this->getOrderedContents($course);

        foreach ($orderedContents as $content) {
            // Konten opsional dianggap selesai walaupun belum dikerjakan
            if (($content->is_optional ?? false) === true) {
                continue;
            }
            // Gunakan definisi tunggal untuk status selesai konten
            if (!$user->hasCompletedContent($content)) {
                return false; // Masih ada yang belum selesai
            }
        }

        return true; // Semua sudah selesai
    }

    // TAMBAHKAN method ini ke ContentController:
    private function checkAndGenerateCertificate(Course $course, User $user)
    {
        Log::info("Checking certificate eligibility for user {$user->id} in course {$course->id}");

        // Cek apakah course punya template sertifikat
        if (!$course->certificate_template_id) {
            Log::info("No certificate template set for course {$course->id}");
            return;
        }

        // Cek progress user
        $progress = $user->courseProgress($course);
        Log::info("User {$user->id} progress: {$progress}%");

        // Syarat untuk sertifikat: progress 100%
        if ($progress >= 100) {
            // Cek apakah sertifikat sudah ada
            $existingCertificate = Certificate::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$existingCertificate) {
                Log::info("Generating new certificate for user {$user->id} in course {$course->id}");
                $this->generateCertificate($course, $user);
            } else {
                Log::info("Certificate already exists for user {$user->id} in course {$course->id}");
            }
        } else {
            Log::info("Certificate conditions not met - Progress: {$progress}%");
        }
    }

    private function generateCertificate(Course $course, User $user)
    {
        $template = $course->certificateTemplate;
        if (!$template) {
            Log::warning("Certificate generation skipped for user {$user->id} in course {$course->id}: No template found.");
            return;
        }

        Log::info("Generating certificate for user {$user->id} in course {$course->id}");

        try {
            // Generate unique certificate code
            $certificateCode = Certificate::generateCertificateCode();

            // Create certificate record first
            $certificate = Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'certificate_template_id' => $template->id,
                'certificate_code' => $certificateCode,
                'issued_at' => now(),
            ]);

            // Generate PDF using the certificate render view
            $pdf = Pdf::loadView('certificates.render-pdf', compact('certificate'))
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'dpi' => 150,
                    'defaultFont' => 'times',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);

            // Create certificates directory if it doesn't exist
            $certificatesDir = 'certificates';
            if (!Storage::disk('public')->exists($certificatesDir)) {
                Storage::disk('public')->makeDirectory($certificatesDir);
            }

            // Save PDF file
            $fileName = $certificateCode . '.pdf';
            $filePath = $certificatesDir . '/' . $fileName;

            Storage::disk('public')->put($filePath, $pdf->output());

            // Update certificate record with file path
            $certificate->update(['path' => $filePath]);

            Log::info("Certificate generated successfully for user {$user->id} in course {$course->id}, file: {$filePath}");

            return $certificate;
        } catch (\Exception $e) {
            Log::error("Certificate generation failed for user {$user->id} in course {$course->id}: " . $e->getMessage());

            // Clean up certificate record if PDF generation failed
            if (isset($certificate)) {
                $certificate->delete();
            }

            return null;
        }
    }

    // ✅ PERBAIKAN UTAMA: Method untuk mendapatkan konten dalam urutan yang benar
    private function getOrderedContents(Course $course)
    {
        // ✅ Cache untuk performance di VPS
        return \Cache::remember("ordered_contents_{$course->id}", 300, function () use ($course) {
            // Ambil semua lessons dengan contents dalam urutan yang benar
            $lessons = $course->lessons()
                ->orderBy('order', 'asc')
                ->with(['contents' => function ($query) {
                    $query->orderBy('order', 'asc');
                }])
                ->get();

            // Gabungkan semua contents dari semua lessons dengan mempertahankan urutan
            $orderedContents = collect();

            foreach ($lessons as $lesson) {
                foreach ($lesson->contents as $content) {
                    // Tambahkan informasi lesson order untuk debugging jika diperlukan
                    $content->lesson_order = $lesson->order;
                    $orderedContents->push($content);
                }
            }

            return $orderedContents;
        });
    }

    // ✅ PERBAIKAN: Method untuk mendapatkan konten yang sudah terbuka
    private function getUnlockedContents(User $user, $orderedContents)
    {
        if ($user->can('update contents') || $user->can('manage own courses')) {
            return $orderedContents;
        }

        $unlocked = collect();

        foreach ($orderedContents as $index => $content) {
            if ($index === 0) {
                $unlocked->push($content);
                continue;
            }

            // Jika ini adalah konten pertama di pelajarannya dan lesson punya prasyarat,
            // pastikan prasyaratnya sudah selesai (kecuali prasyarat lesson ditandai opsional)
            $isFirstInLesson = $orderedContents[$index - 1]->lesson_id !== $content->lesson_id;
            if ($isFirstInLesson) {
                $lesson = $content->lesson; // lazy load jika perlu
                if ($lesson && $lesson->prerequisite) {
                    $prereq = $lesson->prerequisite;
                    // Jika prasyarat lesson tidak opsional, user harus menyelesaikannya
                    if (!($prereq->is_optional ?? false)) {
                        if (!auth()->user()->hasCompletedAllContentsInLesson($prereq)) {
                            break;
                        }
                    }
                }
            }

            $previousContent = $orderedContents[$index - 1];
            $previousCompleted = $this->isContentCompletedForUnlock($user, $previousContent);

            if ($previousCompleted) {
                $unlocked->push($content);
            } else {
                break;
            }
        }

        return $unlocked;
    }

    private function isContentCompletedForUnlock(User $user, Content $content): bool
    {
        // Konten opsional tidak menghalangi konten berikutnya
        if ($content->is_optional) {
            return true;
        }

        // CHECK ATTENDANCE REQUIREMENT FOR SYNCHRONOUS CONTENT
        // If content requires attendance, user must be marked present/excused
        if ($content->attendance_required) {
            $attendance = $user->attendances()
                ->where('content_id', $content->id)
                ->first();

            // No attendance record = content not completed = next content locked
            if (!$attendance) {
                return false;
            }

            // Must be marked as present or excused (not absent or late)
            if (!in_array($attendance->status, ['present', 'excused'])) {
                return false;
            }

            // Check minimum attendance duration if specified
            if ($content->min_attendance_minutes &&
                $attendance->duration_minutes < $content->min_attendance_minutes) {
                return false;
            }

            // Attendance requirement met, content is completed for unlock purposes
            return true;
        }

        if ($content->type === 'quiz' && $content->quiz_id) {
            return $user->quizAttempts()
                ->where('quiz_id', $content->quiz_id)
                ->where('passed', true)
                ->exists();
        } elseif ($content->type === 'essay') {
            $submission = $user->essaySubmissions()
                ->where('content_id', $content->id)
                ->first();

            if (!$submission) {
                return false;
            }

            // Untuk unlock, cukup sudah submit - tidak perlu menunggu grading
            return $submission->answers()->count() > 0;
        } else {
            return $user->completedContents()
                ->where('content_id', $content->id)
                ->exists();
        }
    }

    public function create(Lesson $lesson)
    {
        $this->authorize('update', $lesson->course);
        $content = new Content(['type' => 'text', 'lesson_id' => $lesson->id]);
        $content->quiz = null;
        return view('contents.edit', compact('lesson', 'content'));
    }

    public function store(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson->course);
        return $this->save($request, $lesson, new Content());
    }

    public function edit(Lesson $lesson, Content $content)
    {
        $this->authorize('update', $lesson->course);
        // TAMBAH load essayQuestions
        $content->load(['quiz.questions.options', 'essayQuestions', 'images', 'documents']);
        return view('contents.edit', compact('lesson', 'content'));
    }

    public function update(Request $request, Lesson $lesson, Content $content)
    {
        // ✅ LOG PALING AWAL - Sebelum authorize
        Log::info('=== UPDATE METHOD CALLED ===', [
            'content_id' => $content->id,
            'content_type' => $content->type,
            'lesson_id' => $lesson->id,
            'request_method' => $request->method(),
            'request_type' => $request->input('type')
        ]);

        $this->authorize('update', $lesson->course);

        Log::info('=== AUTHORIZATION PASSED ===', [
            'content_id' => $content->id
        ]);

        return $this->save($request, $lesson, $content);
    }

    private function save(Request $request, Lesson $lesson, Content $content)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(['text', 'video', 'document', 'image', 'quiz', 'essay', 'zoom'])],
            'order' => 'nullable|integer',
            'is_optional' => 'sometimes|boolean',
            'document_access_type' => ['nullable', Rule::in(['both', 'download_only', 'preview_only'])],
            // Attendance fields
            'attendance_required' => 'sometimes|boolean',
            'min_attendance_minutes' => 'nullable|integer|min:1',
            'attendance_notes' => 'nullable|string|max:1000',
        ];

        // Validasi file upload berdasarkan tipe konten
        if ($request->input('type') === 'document') {
            // Izinkan salah satu: file_upload tunggal atau documents[] multiple
            $rules['file_upload'] = [
                $content->exists ? 'nullable' : 'required_without:documents',
                'file',
                'max:102400', // 100MB - lebih realistis untuk dokumen
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,rtf'
            ];
            // Multiple documents optional
            if ($request->hasFile('documents')) {
                $rules['documents'] = ['array', 'max:20'];
                $rules['documents.*'] = ['file', 'max:102400', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,rtf'];
            }
        } elseif ($request->input('type') === 'image') {
            // Izinkan baik single image (file_upload) maupun multiple (images[])
            if ($request->hasFile('file_upload')) {
                $rules['file_upload'] = [
                    'file',
                    'max:10240',
                    'mimes:jpg,jpeg,png,gif,svg,webp'
                ];
            }
            if ($request->hasFile('images')) {
                $rules['images'] = ['array', 'max:20'];
                $rules['images.*'] = ['file', 'image', 'max:10240', 'mimes:jpg,jpeg,png,gif,svg,webp'];
            }
        }

        // 🆕 TAMBAHAN: Validasi untuk scoring_enabled pada essay
        if ($request->has('questions') && !empty($request->input('questions'))) {
            $questionsData = $request->input('questions');
            $hasValidQuestion = false;
            
            foreach ($questionsData as $question) {
                if (!empty($question['text'])) {
                    $hasValidQuestion = true;
                    break;
                }
            }
            
            if ($hasValidQuestion) {
                $rules['questions'] = 'required|array|min:1';
                $rules['questions.*.text'] = 'required|string';
                $rules['questions.*.max_score'] = 'required|integer|min:1|max:10000';
            }
        }

        // PERBAIKAN: Validasi untuk essay questions (hanya jika ada questions)
        if ($request->input('type') === 'essay' && $request->has('questions')) {
            $rules['questions'] = 'required|array|min:1';
            $rules['questions.*.text'] = 'required|string';
            $rules['questions.*.max_score'] = 'required|integer|min:1|max:10000';
        }

        // Tentukan aturan validasi dan sumber data 'body' berdasarkan tipe konten
        $bodySource = null;
        switch ($request->input('type')) {
            case 'text':
            case 'essay': // PERBAIKAN: essay tetap bisa pakai body_text untuk backward compatibility
                $rules['body_text'] = 'nullable|string';
                $bodySource = 'body_text';
                break;
            case 'video':
                $rules['body_video'] = 'nullable|url';
                $bodySource = 'body_video';
                break;
        }

        if ($request->input('type') === 'quiz') {
            // If importing from Excel
            if ($request->hasFile('quiz_excel_file')) {
                $rules['quiz_excel_file'] = 'required|file|mimes:xlsx,xls,csv|max:2048';
            } else {
                // Manual quiz creation
                $rules['quiz'] = 'required|array';
                $rules['quiz.title'] = 'required|string|max:255';
                $rules['time_limit'] = 'nullable|integer|min:0';
            }
        }

        if ($request->input('type') === 'zoom') {
            $rules['zoom_link'] = 'required|url';
            $rules['zoom_meeting_id'] = 'required|string|max:255';
            $rules['zoom_password'] = 'nullable|string|max:255';
            $rules['is_scheduled'] = 'boolean';
            $rules['scheduled_start'] = 'nullable|required_if:is_scheduled,true|date|after:now';
            $rules['scheduled_end'] = 'nullable|required_if:is_scheduled,true|date|after:scheduled_start';
            $rules['timezone'] = 'nullable|string|in:Asia/Jakarta,UTC,Asia/Kuala_Lumpur,Asia/Singapore';
        }

        $validated = $request->validate($rules);

        // ✅ DEBUG: Log semua input yang diterima untuk essay type
        if ($request->input('type') === 'essay') {
            Log::info('=== ESSAY SAVE REQUEST START ===', [
                'content_id' => $content->id ?? 'NEW',
                'content_exists' => $content->exists,
                'request_all' => $request->except(['_token', '_method']),
                'has_body_text' => $request->has('body_text'),
                'body_text_value' => $request->input('body_text'),
                'has_questions' => $request->has('questions'),
                'questions_data' => $request->input('questions')
            ]);
        }

        DB::beginTransaction();
        try {
            $content->lesson_id = $lesson->id;

            // ✅ FIX: Remove 'body' from validated for existing essay content
            // Ini mencegah fill() overwrite body untuk essay yang sudah ada
            if ($validated['type'] === 'essay' && $content->exists) {
                // Backup old body sebelum fill()
                $preservedBody = $content->body;

                $content->fill($validated);

                // Restore body yang di-preserve
                $content->body = $preservedBody;

                Log::info('Essay body preserved during fill()', [
                    'content_id' => $content->id,
                    'preserved_body' => $preservedBody
                ]);
            } else {
                // Normal fill untuk tipe lain atau essay baru
                $content->fill($validated);
            }

            // Pastikan flag opsional terset sesuai input (default false)
            $content->is_optional = (bool) ($request->boolean('is_optional'));

            // ✅ ATTENDANCE: Explicitly set attendance_required boolean (handle unchecked state)
            $content->attendance_required = (bool) ($request->boolean('attendance_required'));

            // If attendance not required, clear related fields
            if (!$content->attendance_required) {
                $content->min_attendance_minutes = null;
                $content->attendance_notes = null;
            }

            // 🆕 TAMBAHAN: Set essay settings berdasarkan review_mode
            if ($validated['type'] === 'essay') {
                $reviewMode = $request->input('review_mode', 'scoring');
                
                // Set properties berdasarkan review mode
                switch ($reviewMode) {
                    case 'scoring':
                        $content->scoring_enabled = true;
                        $content->requires_review = true;
                        break;
                    case 'feedback_only':
                        $content->scoring_enabled = false;
                        $content->requires_review = true;
                        break;
                    case 'no_review':
                        $content->scoring_enabled = false;
                        $content->requires_review = false;
                        break;
                    default:
                        // Fallback to old system if review_mode not provided
                        $content->scoring_enabled = $request->boolean('scoring_enabled', true);
                        $content->requires_review = true;
                        break;
                }
                
                $content->grading_mode = $request->input('grading_mode', 'individual');
                
                // Log untuk debug
                Log::info('Essay settings saved', [
                    'review_mode' => $reviewMode,
                    'scoring_enabled' => $content->scoring_enabled,
                    'requires_review' => $content->requires_review,
                    'grading_mode' => $content->grading_mode
                ]);
            }

            // ✅ REFACTOR: Simplified body handling - NEVER touch body for existing essay content
            if ($bodySource) {
                // 🚨 CRITICAL: Untuk essay type yang sudah exist, JANGAN PERNAH TOUCH BODY!
                // Essay content disimpan di essay_questions table, bukan di body column
                if ($validated['type'] === 'essay' && $content->exists) {
                    // SKIP - Jangan touch body sama sekali untuk existing essay
                    Log::info('=== ESSAY EDIT - SKIPPING BODY ===', [
                        'content_id' => $content->id,
                        'current_body' => $content->body,
                        'action' => 'PRESERVED - No changes to body column'
                    ]);
                    // Body akan tetap dengan nilai yang sudah ada di database
                } else {
                    // Untuk tipe lain (text, video) ATAU essay baru, set body seperti biasa
                    $content->body = $request->input($bodySource);

                    if ($validated['type'] === 'essay' && !$content->exists) {
                        Log::info('New essay - setting initial body', ['body' => $content->body]);
                    }
                }
            } elseif ($validated['type'] === 'zoom') {
                $zoomData = [
                    'link' => $validated['zoom_link'],
                    'meeting_id' => $validated['zoom_meeting_id'],
                    'password' => $validated['zoom_password'] ?? '',
                ];

                if ($request->boolean('is_scheduled')) {
                    $timezone = $request->input('timezone', 'Asia/Jakarta');
                    $zoomData['is_scheduled'] = true;
                    $zoomData['timezone'] = $timezone;

                    // Convert to UTC for storage
                    $content->scheduled_start = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('scheduled_start'), $timezone)->utc();
                    $content->scheduled_end = Carbon::createFromFormat('Y-m-d\TH:i', $request->input('scheduled_end'), $timezone)->utc();
                    $content->is_scheduled = true;
                } else {
                    $content->is_scheduled = false;
                    $content->scheduled_start = null;
                    $content->scheduled_end = null;
                }

                $content->body = json_encode($zoomData);
            } else {
                // ✅ GUARD: Jangan set body = null untuk existing essay
                if (!($validated['type'] === 'essay' && $content->exists)) {
                    $content->body = null;
                }
            }

            if ($request->hasFile('file_upload')) {
                if ($content->file_path) Storage::disk('public')->delete($content->file_path);
                $content->file_path = $request->file('file_upload')->store('content_files', 'public');
            }

            // 🆕 Multiple images handling for image content (defer create until after $content->save())
            $deferredImagePaths = [];
            if ($validated['type'] === 'image' && $request->hasFile('images')) {
                foreach ($request->file('images') as $imgFile) {
                    if (!$imgFile->isValid()) continue;
                    $deferredImagePaths[] = $imgFile->store('content_files', 'public');
                }
                // Set file_path ke gambar pertama jika belum ada (backward compat)
                if (!$content->file_path && !empty($deferredImagePaths)) {
                    $content->file_path = $deferredImagePaths[0];
                }
            }

            // Multiple documents handling (defer create until after $content->save())
            $deferredDocumentFiles = [];
            if ($validated['type'] === 'document' && $request->hasFile('documents')) {
                foreach ($request->file('documents') as $docFile) {
                    if (!$docFile->isValid()) continue;
                    $stored = $docFile->store('content_files', 'public');
                    $deferredDocumentFiles[] = [
                        'path' => $stored,
                        'name' => $docFile->getClientOriginalName(),
                    ];
                }
                if (!$content->file_path && !empty($deferredDocumentFiles)) {
                    $content->file_path = $deferredDocumentFiles[0]['path'];
                }
            }

            // Set order dengan lebih hati-hati
            if (!$content->exists) {
                $lastOrder = $lesson->contents()->max('order') ?? 0;
                $content->order = $lastOrder + 1;
            } else {
                $content->order = $request->input('order', $content->order ?? 1);
            }

            if ($validated['type'] === 'quiz') {
                // Check if import from Excel
                if ($request->hasFile('quiz_excel_file')) {
                    try {
                        $import = new \App\Imports\QuizImport($lesson->id, Auth::id());
                        \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('quiz_excel_file'));

                        $errors = $import->getErrors();
                        $successCount = $import->getSuccessCount();

                        if ($successCount > 0) {
                            // Get the first imported quiz to link with this content
                            $importedQuiz = \App\Models\Quiz::where('lesson_id', $lesson->id)
                                ->where('user_id', Auth::id())
                                ->latest()
                                ->first();

                            if ($importedQuiz) {
                                $content->quiz_id = $importedQuiz->id;
                            }

                            if (count($errors) > 0) {
                                session()->flash('import_errors', $errors);
                            }
                        } else {
                            throw new \Exception('No quiz was successfully imported. ' . implode('; ', $errors));
                        }
                    } catch (\Exception $e) {
                        throw new \Exception('Import failed: ' . $e->getMessage());
                    }
                } elseif ($request->has('quiz')) {
                    // Manual quiz creation
                    $quizData = $request->input('quiz');
                    $quizData['time_limit'] = $validated['time_limit'] ?? null;
                    $quiz = $this->saveQuiz($quizData, $lesson, $content->quiz_id);
                    $content->quiz_id = $quiz->id;
                }
            } else {
                if ($content->quiz) $content->quiz->delete();
                $content->quiz_id = null;
            }

            // Log body sebelum save pertama
            if ($validated['type'] === 'essay' && $content->exists) {
                Log::info('BEFORE FIRST SAVE', ['body' => $content->body]);
            }

            // ✅ ENHANCED LOGGING: Track before/after changes
            $isCreating = !$content->exists;
            $originalData = $isCreating ? null : $content->getOriginal();

            $content->save();

            // ✅ LOG CONTENT ACTIVITY WITH BEFORE/AFTER
            if ($isCreating) {
                \App\Models\ActivityLog::log('content_created', [
                    'description' => "Created new {$content->type} content: {$content->title}",
                    'metadata' => [
                        'content_id' => $content->id,
                        'content_title' => $content->title,
                        'content_type' => $content->type,
                        'lesson_id' => $content->lesson_id,
                        'lesson_title' => $content->lesson->title ?? null,
                        'course_id' => $content->lesson->course_id ?? null,
                        'course_title' => $content->lesson->course->title ?? null,
                        'is_optional' => $content->is_optional ?? false,
                        'attendance_required' => $content->attendance_required ?? false,
                        'file_info' => $content->file_path ? [
                            'file_name' => basename($content->file_path),
                            'file_path' => $content->file_path,
                        ] : null,
                    ]
                ]);
            } else {
                // Track what changed
                $changes = [];
                $fields = ['title', 'description', 'type', 'order', 'is_optional', 'attendance_required', 'file_path', 'url'];

                foreach ($fields as $field) {
                    if (isset($originalData[$field]) && $originalData[$field] != $content->$field) {
                        $changes[$field] = [
                            'before' => $originalData[$field],
                            'after' => $content->$field
                        ];
                    }
                }

                \App\Models\ActivityLog::log('content_updated', [
                    'description' => "Updated {$content->type} content: {$content->title}" . (count($changes) > 0 ? " (" . implode(', ', array_keys($changes)) . " changed)" : ""),
                    'metadata' => [
                        'content_id' => $content->id,
                        'content_title' => $content->title,
                        'content_type' => $content->type,
                        'lesson_id' => $content->lesson_id,
                        'lesson_title' => $content->lesson->title ?? null,
                        'course_id' => $content->lesson->course_id ?? null,
                        'course_title' => $content->lesson->course->title ?? null,
                        'changes' => $changes,
                        'changed_fields' => array_keys($changes),
                    ]
                ]);
            }

            // Create related images after content has an ID
            if ($validated['type'] === 'image' && !empty($deferredImagePaths)) {
                $orderBase = (int) ($content->images()->max('order') ?? 0);
                foreach (array_values($deferredImagePaths) as $idx => $path) {
                    $content->images()->create([
                        'file_path' => $path,
                        'order' => $orderBase + $idx + 1,
                    ]);
                }
            }

            // Create related documents after content has an ID
            if ($validated['type'] === 'document' && !empty($deferredDocumentFiles)) {
                $orderBaseDocs = (int) ($content->documents()->max('order') ?? 0);
                foreach (array_values($deferredDocumentFiles) as $idx => $doc) {
                    $content->documents()->create([
                        'file_path' => $doc['path'],
                        'original_name' => $doc['name'] ?? null,
                        'order' => $orderBaseDocs + $idx + 1,
                    ]);
                }
            }

            // 🆕 Manage deletions and reorder for existing images
            if ($validated['type'] === 'image') {
                // Deletions
                $deleteIds = collect($request->input('delete_images', []))
                    ->filter(fn($v) => is_numeric($v))
                    ->map(fn($v) => (int) $v)
                    ->values();
                if ($deleteIds->isNotEmpty()) {
                    $toDelete = $content->images()->whereIn('id', $deleteIds)->get();
                    foreach ($toDelete as $img) {
                        if ($img->file_path) { try { Storage::disk('public')->delete($img->file_path); } catch (\Throwable $e) {} }
                        $img->delete();
                    }
                }

                // Reorder (expects comma-separated IDs in desired order)
                $orderStr = (string) $request->input('image_order', '');
                if ($orderStr !== '') {
                    $ids = collect(explode(',', $orderStr))
                        ->filter(fn($v) => trim($v) !== '')
                        ->map(fn($v) => (int) $v)
                        ->values();
                    $pos = 1;
                    foreach ($ids as $id) {
                        // Only reorder images that still belong to this content
                        $content->images()->where('id', $id)->update(['order' => $pos]);
                        $pos++;
                    }
                }

                // Ensure file_path points to first image if missing or deleted
                $firstImage = $content->images()->orderBy('order')->first();
                $content->file_path = $firstImage ? $firstImage->file_path : $content->file_path;
                if (!$firstImage && $content->file_path) {
                    // If file_path exists but images table is empty, create a record to normalize
                    $content->images()->create([
                        'file_path' => $content->file_path,
                        'order' => 1,
                    ]);
                }
                $content->save();
            }

            // Manage deletions and reorder for existing documents (when type=document)
            if ($validated['type'] === 'document') {
                // Deletions
                $deleteIds = collect($request->input('delete_documents', []))
                    ->filter(fn($v) => is_numeric($v))
                    ->map(fn($v) => (int) $v)
                    ->values();
                if ($deleteIds->isNotEmpty()) {
                    $toDelete = $content->documents()->whereIn('id', $deleteIds)->get();
                    foreach ($toDelete as $doc) {
                        if ($doc->file_path) { try { Storage::disk('public')->delete($doc->file_path); } catch (\Throwable $e) {} }
                        $doc->delete();
                    }
                }

                // Reorder
                $orderStr = (string) $request->input('document_order', '');
                if ($orderStr !== '') {
                    $ids = collect(explode(',', $orderStr))
                        ->filter(fn($v) => trim($v) !== '')
                        ->map(fn($v) => (int) $v)
                        ->values();
                    $pos = 1;
                    foreach ($ids as $id) {
                        $content->documents()->where('id', $id)->update(['order' => $pos]);
                        $pos++;
                    }
                }

                // Ensure file_path points to first document if present
                $firstDoc = $content->documents()->orderBy('order')->first();
                if ($firstDoc) {
                    $content->file_path = $firstDoc->file_path;
                    $content->save();
                }
            }

            // Log body setelah save pertama
            if ($validated['type'] === 'essay' && $content->wasRecentlyCreated === false) {
                Log::info('AFTER FIRST SAVE', ['body' => $content->fresh()->body]);
            }

            // ✅ FIX: Handle essay questions HANYA untuk CREATE mode (content baru tanpa existing questions)
            // Untuk EDIT mode, pertanyaan baru ditambahkan via form terpisah (EssayQuestionController)
            if ($validated['type'] === 'essay' && $request->has('questions')) {
                // Cek apakah ada pertanyaan yang valid (tidak kosong)
                $hasValidQuestions = false;
                foreach ($validated['questions'] as $questionData) {
                    if (!empty($questionData['text']) && trim($questionData['text']) !== '') {
                        $hasValidQuestions = true;
                        break;
                    }
                }

                // HANYA proses jika:
                // 1. Ada pertanyaan valid DI REQUEST
                // 2. Content BELUM punya existing questions (CREATE mode)
                if ($hasValidQuestions && !$content->essayQuestions()->exists()) {
                    $this->saveEssayQuestions($content, $validated['questions']);
                    // ❌ TIDAK perlu set body = null atau save lagi
                    // Body handling sudah dilakukan di line 470-514 dengan logic yang benar
                }
            }

            DB::commit();

            // ✅ DEBUG: Log hasil akhir setelah commit untuk essay
            if ($validated['type'] === 'essay') {
                $finalContent = $content->fresh(); // Reload from DB
                Log::info('=== ESSAY SAVE REQUEST END ===', [
                    'content_id' => $finalContent->id,
                    'final_body' => $finalContent->body,
                    'body_is_null' => is_null($finalContent->body),
                    'body_is_empty' => empty($finalContent->body),
                    'essay_questions_count' => $finalContent->essayQuestions()->count()
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Essay save failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('courses.show', $lesson->course)->with('success', 'Konten berhasil disimpan.');
    }

    private function saveEssayQuestions($content, $questionsData)
    {
        // ✅ FIX: Method ini sekarang HANYA untuk menambah pertanyaan BARU
        // Update existing questions di-handle oleh EssayQuestionController::update()

        $existingQuestionsCount = $content->essayQuestions()->count();
        $newQuestionsData = array_values($questionsData);

        DB::transaction(function() use ($content, $existingQuestionsCount, $newQuestionsData) {
            // ✅ Tambahkan pertanyaan baru mulai dari order setelah existing questions
            foreach ($newQuestionsData as $index => $questionData) {
                // Skip empty questions
                if (empty($questionData['text'])) {
                    continue;
                }

                $order = $existingQuestionsCount + $index + 1;
                $maxScore = $content->scoring_enabled ? ($questionData['max_score'] ?? 100) : 0;

                // ✅ Selalu CREATE baru, jangan UPDATE existing
                $content->essayQuestions()->create([
                    'question' => $questionData['text'],
                    'order' => $order,
                    'max_score' => $maxScore,
                    'is_active' => true
                ]);

                Log::info("Created new essay question", [
                    'content_id' => $content->id,
                    'order' => $order,
                    'question' => substr($questionData['text'], 0, 50)
                ]);
            }
        });
    }

    private function saveQuiz(array $quizData, Lesson $lesson, ?int $quizId): Quiz
    {
        $quiz = Quiz::updateOrCreate(
            ['id' => $quizId],
            [
                'lesson_id' => $lesson->id,
                'user_id' => Auth::id(),
                'title' => $quizData['title'],
                'description' => $quizData['description'] ?? null,
                'passing_percentage' => $quizData['passing_percentage'] ?? 70,
                'time_limit' => $quizData['time_limit'] ?? null,
                'status' => $quizData['status'] ?? 'draft',
                'show_answers_after_attempt' => filter_var($quizData['show_answers_after_attempt'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'enable_leaderboard' => filter_var($quizData['enable_leaderboard'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ]
        );

        $questionIdsToKeep = [];
        if (!empty($quizData['questions'])) {
            foreach ($quizData['questions'] as $qData) {
                if (empty($qData['question_text'])) continue;

                $question = Question::updateOrCreate(
                    ['id' => $qData['id'] ?? null, 'quiz_id' => $quiz->id],
                    ['question_text' => $qData['question_text'], 'type' => $qData['type'], 'marks' => $qData['marks']]
                );
                $questionIdsToKeep[] = $question->id;

                $optionIdsToKeep = [];
                if ($qData['type'] === 'multiple_choice' && !empty($qData['options'])) {
                    foreach ($qData['options'] as $oData) {
                        if (empty($oData['option_text'])) continue;
                        $option = Option::updateOrCreate(
                            ['id' => $oData['id'] ?? null, 'question_id' => $question->id],
                            [
                                'option_text' => $oData['option_text'],
                                'is_correct' => filter_var($oData['is_correct'] ?? false, FILTER_VALIDATE_BOOLEAN)
                            ]
                        );
                        $optionIdsToKeep[] = $option->id;
                    }
                } elseif ($qData['type'] === 'true_false') {
                    $question->options()->delete();
                    $optionTrue = $question->options()->create([
                        'option_text' => 'True',
                        'is_correct' => ($qData['correct_answer_tf'] === 'true')
                    ]);
                    $optionFalse = $question->options()->create([
                        'option_text' => 'False',
                        'is_correct' => ($qData['correct_answer_tf'] === 'false')
                    ]);
                    $optionIdsToKeep = [$optionTrue->id, $optionFalse->id];
                }
                $question->options()->whereNotIn('id', $optionIdsToKeep)->delete();
            }
        }
        $quiz->questions()->whereNotIn('id', $questionIdsToKeep)->delete();

        return $quiz;
    }

    public function destroy(Lesson $lesson, Content $content)
    {
        $this->authorize('update', $lesson->course);

        // Store data before deletion for logging
        $contentData = [
            'content_id' => $content->id,
            'content_title' => $content->title,
            'content_type' => $content->type,
            'lesson_id' => $content->lesson_id,
            'course_id' => $lesson->course_id,
            'course_title' => $lesson->course->title,
        ];

        if ($content->file_path) Storage::disk('public')->delete($content->file_path);
        if ($content->quiz) $content->quiz->delete();
        $content->delete();

        // ✅ LOG CONTENT DELETION
        \App\Models\ActivityLog::log('content_deleted', [
            'description' => "Deleted content: {$contentData['content_title']} ({$contentData['content_type']})",
            'metadata' => $contentData
        ]);

        return redirect()->route('courses.show', $lesson->course)->with('success', 'Konten berhasil dihapus!');
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'contents' => 'required|array',
            'contents.*' => 'integer|exists:contents,id',
        ]);

        $firstContent = Content::find($request->contents[0]);
        if ($firstContent) {
            $this->authorize('update', $firstContent->lesson->course);
        }

        foreach ($request->contents as $index => $contentId) {
            Content::where('id', $contentId)->update(['order' => $index + 1]);
        }

        return response()->json(['status' => 'success', 'message' => 'Urutan konten berhasil diperbarui.']);
    }

    /**
     * ✅ TAMBAHAN: Method helper untuk debugging unlock logic
     */
    public function debugUnlock(Course $course)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $user = Auth::user();
        $orderedContents = $this->getOrderedContents($course);
        $unlockedContents = $this->getUnlockedContents($user, $orderedContents);

        $debug = [
            'user_id' => $user->id,
            'user_role' => $user->roles->pluck('name'),
            'total_contents' => $orderedContents->count(),
            'unlocked_contents' => $unlockedContents->count(),
            'contents' => $orderedContents->map(function ($content, $index) use ($user, $unlockedContents) {
                $isUnlocked = $unlockedContents->contains('id', $content->id);
                $isCompleted = false;

                if ($content->type === 'quiz' && $content->quiz_id) {
                    $isCompleted = $user->quizAttempts()
                        ->where('quiz_id', $content->quiz_id)
                        ->where('passed', true)
                        ->exists();
                } elseif ($content->type === 'essay') {
                    $isCompleted = $user->essaySubmissions()
                        ->where('content_id', $content->id)
                        ->exists();
                } else {
                    $isCompleted = $user->completedContents()
                        ->where('content_id', $content->id)
                        ->exists();
                }

                return [
                    'index' => $index,
                    'id' => $content->id,
                    'title' => $content->title,
                    'type' => $content->type,
                    'lesson_id' => $content->lesson_id,
                    'order' => $content->order,
                    'is_unlocked' => $isUnlocked,
                    'is_completed' => $isCompleted,
                ];
            })
        ];

        return response()->json($debug);
    }

    public function duplicate(Lesson $lesson, Content $content)
    {
        $this->authorize('update', $lesson->course);

        try {
            $newContent = $content->duplicate();
            // The trait handles the duplication, we just ensure it's linked to the correct lesson.
            $newContent->lesson_id = $lesson->id;
            $newContent->save();

            return redirect()->route('courses.show', $lesson->course)->with('success', 'Content duplicated successfully.');
        } catch (\Exception $e) {
            // Optional: Log the exception
            return redirect()->route('courses.show', $lesson->course)->with('error', 'Failed to duplicate content: ' . $e->getMessage());
        }
    }
}
