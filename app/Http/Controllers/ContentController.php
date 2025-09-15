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

        if (!$unlockedContents->contains('id', $content->id) && !$user->hasRole(['super-admin', 'instructor'])) {
            return redirect()->back()->with('error', 'Anda harus menyelesaikan materi sebelumnya terlebih dahulu.');
        }

        // TAMBAH load untuk essayQuestions dan audioLesson
        $content->load('lesson.course', 'discussions.user', 'discussions.replies.user', 'quiz.questions.options', 'essayQuestions', 'audioLesson');

        if ($user->hasRole(['super-admin', 'instructor'])) {
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

        return view('contents.show', compact('content', 'course', 'unlockedContents', 'hasPassedQuizBefore', 'orderedContents'));
    }

    /**
     * ✅ TAMBAHAN: Method untuk menandai konten selesai dan lanjut ke berikutnya
     */
    public function completeAndContinue(Content $content)
    {
        $user = Auth::user();
        $lesson = $content->lesson;
        $course = $lesson->course;

        // Tandai konten saat ini sebagai selesai
        $user->completedContents()->syncWithoutDetaching([
            $content->id => ['completed' => true, 'completed_at' => now()]
        ]);

        // ✅ FIX: Cari next content dengan urutan yang benar ACROSS LESSONS
        $orderedContents = $this->getOrderedContents($course);

        $currentIndex = $orderedContents->search(function ($item) use ($content) {
            return $item->id === $content->id;
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

            if (!$isCompleted) {
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

        // Cek apakah semua graded items sudah dinilai
        $allGradedItemsMarked = $user->areAllGradedItemsMarked($course);
        Log::info("All graded items marked: " . ($allGradedItemsMarked ? 'Yes' : 'No'));

        // Syarat untuk mendapat sertifikat: progress 100% dan semua item graded sudah dinilai
        if ($progress >= 100 && $allGradedItemsMarked) {
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
            Log::info("Certificate conditions not met - Progress: {$progress}%, All graded: " . ($allGradedItemsMarked ? 'Yes' : 'No'));
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
        if ($user->hasRole(['super-admin', 'instructor'])) {
            return $orderedContents;
        }

        $unlocked = collect();

        foreach ($orderedContents as $index => $content) {
            if ($index === 0) {
                $unlocked->push($content);
                continue;
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
        $content->load(['quiz.questions.options', 'essayQuestions']);
        return view('contents.edit', compact('lesson', 'content'));
    }

    public function update(Request $request, Lesson $lesson, Content $content)
    {
        $this->authorize('update', $lesson->course);
        return $this->save($request, $lesson, $content);
    }

    private function save(Request $request, Lesson $lesson, Content $content)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(['text', 'video', 'document', 'image', 'quiz', 'essay', 'zoom', 'audio'])],
            'order' => 'nullable|integer',
            'file_upload' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt|max:102400',
        ];

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
                $rules['video_file'] = 'nullable|file|mimes:mp4,mov,avi,mkv,webm,flv,wmv,3gp|max:512000'; // 500MB max
                $bodySource = 'body_video';
                break;
            case 'audio':
                // Base audio rules - only required for new content
                if (!$content->exists || $request->has('audio_type')) {
                    $rules['audio_type'] = 'required|string|in:simple,existing_lesson,new_lesson';
                } else {
                    $rules['audio_type'] = 'nullable|string|in:simple,existing_lesson,new_lesson';
                }

                // Conditional rules based on audio_type
                if ($request->input('audio_type') === 'existing_lesson') {
                    $rules['audio_lesson_id'] = 'required|exists:audio_lessons,id';
                } else {
                    $rules['audio_file'] = 'nullable|file|mimes:mp3,wav,m4a,aac|max:51200'; // 50MB max
                    $rules['audio_transcript'] = 'nullable|string';
                    $rules['audio_difficulty'] = 'nullable|string|in:beginner,intermediate,advanced';

                    if ($request->input('audio_type') === 'simple') {
                        $rules['audio_has_quiz'] = 'nullable|boolean';
                        $rules['audio_time_limit'] = 'nullable|integer|min:0';
                        $rules['audio_quiz_types'] = 'nullable|array';
                        $rules['audio_quiz_types.*'] = 'nullable|string|in:multiple_choice,fill_blank,true_false,listening_comprehension';
                    } elseif ($request->input('audio_type') === 'new_lesson') {
                        $rules['new_lesson_title'] = 'required|string|max:255';
                        $rules['new_lesson_description'] = 'nullable|string';
                        $rules['new_lesson_category'] = 'required|string|in:conversation,listening,pronunciation,grammar,vocabulary,business,academic,general';
                    }
                }
                $bodySource = 'audio_transcript';
                break;
        }

        if ($request->input('type') === 'quiz') {
            $rules['quiz'] = 'required|array';
            $rules['quiz.title'] = 'required|string|max:255';
            $rules['time_limit'] = 'nullable|integer|min:0';
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

        DB::beginTransaction();
        try {
            $content->lesson_id = $lesson->id;
            $content->fill($validated);

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

            // PERBAIKAN: Secara eksplisit atur kolom 'body' dari sumber yang benar
            if ($bodySource) {
                $content->body = $request->input($bodySource);
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
                $content->body = null;
            }

            if ($request->hasFile('file_upload')) {
                if ($content->file_path) Storage::disk('public')->delete($content->file_path);
                $content->file_path = $request->file('file_upload')->store('content_files', 'public');

                // Save document metadata
                $fileMetadata = [
                    'file_size' => $request->file('file_upload')->getSize(),
                    'mime_type' => $request->file('file_upload')->getMimeType(),
                    'original_name' => $request->file('file_upload')->getClientOriginalName(),
                    'uploaded_at' => now()->toISOString()
                ];

                // Store metadata in audio_metadata field (reusing existing field)
                $content->audio_metadata = $fileMetadata;
            }

            // Handle audio file upload
            if ($request->hasFile('audio_file')) {
                if ($content->file_path) Storage::disk('public')->delete($content->file_path);
                $content->file_path = $request->file('audio_file')->store('audio/lessons', 'public');

                // Save audio metadata
                $content->audio_difficulty_level = $request->input('audio_difficulty', 'beginner');
                $audioMetadata = [
                    'file_size' => $request->file('audio_file')->getSize(),
                    'mime_type' => $request->file('audio_file')->getMimeType(),
                    'original_name' => $request->file('audio_file')->getClientOriginalName(),
                    'uploaded_at' => now()->toISOString()
                ];
                $content->audio_metadata = $audioMetadata;
            }

            // Handle video file upload
            if ($request->hasFile('video_file')) {
                if ($content->file_path) Storage::disk('public')->delete($content->file_path);
                $content->file_path = $request->file('video_file')->store('video/lessons', 'public');

                // Clear body_video URL if file is uploaded
                $content->body = null;

                // Save video metadata
                $videoMetadata = [
                    'file_size' => $request->file('video_file')->getSize(),
                    'mime_type' => $request->file('video_file')->getMimeType(),
                    'original_name' => $request->file('video_file')->getClientOriginalName(),
                    'uploaded_at' => now()->toISOString()
                ];

                // Try to get video duration using getID3 if available
                try {
                    if (class_exists('getID3')) {
                        $getID3 = new \getID3;
                        $fileInfo = $getID3->analyze($request->file('video_file')->getPathname());
                        if (isset($fileInfo['playtime_seconds'])) {
                            $videoMetadata['duration_seconds'] = (int) $fileInfo['playtime_seconds'];
                        }
                    }
                } catch (\Exception $e) {
                    // Ignore errors in metadata extraction
                }

                // Store metadata in audio_metadata field (reusing existing field)
                $content->audio_metadata = $videoMetadata;
            }

            // Handle audio learning integration
            if ($validated['type'] === 'audio') {
                $audioType = $request->input('audio_type', 'simple');

                switch ($audioType) {
                    case 'existing_lesson':
                        // Link to existing audio lesson
                        $content->is_audio_learning = true;
                        $content->audio_lesson_id = $request->input('audio_lesson_id');
                        $content->quiz_id = null; // Use audio lesson's exercises instead
                        break;

                    case 'new_lesson':
                        // Create new audio lesson and link
                        $audioLesson = $this->createAudioLesson($request);
                        $content->is_audio_learning = true;
                        $content->audio_lesson_id = $audioLesson->id;
                        $content->quiz_id = null; // Use audio lesson's exercises instead
                        break;

                    case 'simple':
                    default:
                        // Simple audio content with basic quiz
                        $content->is_audio_learning = false;
                        $content->audio_lesson_id = null;

                        // Debug: Log audio quiz request data
                        Log::info('Audio Content Processing', [
                            'has_audio_quiz' => $request->boolean('audio_has_quiz'),
                            'audio_quiz_types' => $request->input('audio_quiz_types', []),
                            'audio_time_limit' => $request->input('audio_time_limit'),
                            'content_id' => $content->id ?? 'new'
                        ]);

                        if ($request->boolean('audio_has_quiz')) {
                            // Create or update quiz for audio content
                            $quizData = [
                                'title' => $validated['title'] . ' - Audio Quiz',
                                'description' => 'Quiz interaktif berdasarkan audio: ' . ($validated['description'] ?? ''),
                                'time_limit' => $request->input('audio_time_limit') ?: null,
                                'total_marks' => 0, // Will be calculated after questions are created
                                'pass_marks' => 0,  // Will be set to 60% of total_marks
                                'status' => 'draft',
                                'show_answers_after_attempt' => true,
                            ];

                            $quiz = $this->saveQuiz($quizData, $lesson, $content->quiz_id);
                            $content->quiz_id = $quiz->id;

                            // Create placeholder questions based on selected types
                            $quizTypes = $request->input('audio_quiz_types', ['multiple_choice']);
                            $this->createAudioQuizPlaceholders($quiz, $quizTypes);

                            // Debug logging
                            Log::info('Audio Quiz Created', [
                                'quiz_id' => $quiz->id,
                                'content_id' => $content->id ?? 'new',
                                'quiz_types' => $quizTypes,
                                'questions_count' => $quiz->questions()->count(),
                                'total_marks' => $quiz->total_marks
                            ]);
                        } else {
                            // Remove quiz if audio_has_quiz is disabled
                            if ($content->quiz) {
                                $content->quiz->delete();
                            }
                            $content->quiz_id = null;
                        }
                        break;
                }
            }

            // Set order dengan lebih hati-hati
            if (!$content->exists) {
                $lastOrder = $lesson->contents()->max('order') ?? 0;
                $content->order = $lastOrder + 1;
            } else {
                $content->order = $request->input('order', $content->order ?? 1);
            }

            if ($validated['type'] === 'quiz' && $request->has('quiz')) {
                $quizData = $request->input('quiz');
                $quizData['time_limit'] = $validated['time_limit'] ?? null;
                $quiz = $this->saveQuiz($quizData, $lesson, $content->quiz_id);
                $content->quiz_id = $quiz->id;
            } elseif ($validated['type'] !== 'audio') {
                // Don't reset quiz_id for audio content as it might have been set in the audio case above
                if ($content->quiz) $content->quiz->delete();
                $content->quiz_id = null;
            }

            $content->save();

            // PERBAIKAN: Handle essay questions HANYA jika ada questions data
            if ($validated['type'] === 'essay' && $request->has('questions') && !empty($validated['questions'])) {
                $this->saveEssayQuestions($content, $validated['questions']);
                $content->body = null;
                $content->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('courses.show', $lesson->course)->with('success', 'Konten berhasil disimpan.');
    }

    private function saveEssayQuestions($content, $questionsData)
    {
        $existingQuestions = $content->allEssayQuestions()->orderBy('order')->get();
        $newQuestionsData = array_values($questionsData);
        
        DB::transaction(function() use ($content, $existingQuestions, $newQuestionsData) {
            foreach ($newQuestionsData as $index => $questionData) {
                $order = $index + 1;
                $maxScore = $content->scoring_enabled ? ($questionData['max_score'] ?? 100) : 0;
                
                if (isset($existingQuestions[$index])) {
                    // Update existing question
                    $existingQuestions[$index]->update([
                        'question' => $questionData['text'],
                        'order' => $order,
                        'max_score' => $maxScore,
                        'is_active' => true
                    ]);
                } else {
                    // Create new question
                    $content->essayQuestions()->create([
                        'question' => $questionData['text'],
                        'order' => $order,
                        'max_score' => $maxScore,
                        'is_active' => true
                    ]);
                }
            }
            
            // Soft delete questions yang berlebih
            if ($existingQuestions->count() > count($newQuestionsData)) {
                $questionsToDeactivate = $existingQuestions->slice(count($newQuestionsData));
                
                foreach ($questionsToDeactivate as $question) {
                    $hasAnswers = \App\Models\EssayAnswer::where('question_id', $question->id)->exists();
                    
                    if ($hasAnswers) {
                        // Soft delete - preserve data
                        $question->update(['is_active' => false]);
                        Log::info("Deactivated question {$question->id} - has existing answers");
                    } else {
                        // Hard delete - no answers
                        $question->delete();
                        Log::info("Deleted unused question {$question->id}");
                    }
                }
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
                'total_marks' => $quizData['total_marks'] ?? 0,
                'pass_marks' => $quizData['pass_marks'] ?? 0,
                'time_limit' => $quizData['time_limit'] ?? null,
                'status' => $quizData['status'] ?? 'draft',
                'show_answers_after_attempt' => filter_var($quizData['show_answers_after_attempt'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ]
        );

        $questionIdsToKeep = [];
        if (!empty($quizData['questions'])) {
            foreach ($quizData['questions'] as $qData) {
                if (empty($qData['question_text'])) continue;

                $questionAttributes = [
                    'question_text' => $qData['question_text'],
                    'type' => $qData['type'],
                    'marks' => $qData['marks']
                ];

                // Add additional fields for different question types
                if ($qData['type'] === 'fill_blank') {
                    $questionAttributes['correct_answer'] = $qData['correct_answer'] ?? '';
                    $questionAttributes['alternative_answers'] = $qData['alternative_answers'] ?? '';
                } elseif ($qData['type'] === 'listening_comprehension') {
                    $questionAttributes['comprehension_type'] = $qData['comprehension_type'] ?? 'text';
                    $questionAttributes['expected_answer'] = $qData['expected_answer'] ?? '';
                }

                $question = Question::updateOrCreate(
                    ['id' => $qData['id'] ?? null, 'quiz_id' => $quiz->id],
                    $questionAttributes
                );
                $questionIdsToKeep[] = $question->id;

                $optionIdsToKeep = [];
                if (($qData['type'] === 'multiple_choice' || ($qData['type'] === 'listening_comprehension' && $qData['comprehension_type'] === 'multiple_choice')) && !empty($qData['options'])) {
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

    private function createAudioQuizPlaceholders(Quiz $quiz, array $quizTypes)
    {
        // Remove existing questions to create fresh placeholders
        $quiz->questions()->delete();

        $questionTemplates = [
            'multiple_choice' => [
                'question_text' => 'Based on the audio, which of the following statements is correct?',
                'type' => 'multiple_choice',
                'marks' => 10,
                'options' => [
                    ['text' => 'Option A (edit this)', 'correct' => false],
                    ['text' => 'Option B (edit this)', 'correct' => true],
                    ['text' => 'Option C (edit this)', 'correct' => false],
                    ['text' => 'Option D (edit this)', 'correct' => false],
                ]
            ],
            'fill_blank' => [
                'question_text' => 'Fill in the missing word from the audio: "The speaker mentioned that ____ is very important."',
                'type' => 'fill_blank',
                'marks' => 10,
                'correct_answer' => 'education',
                'alternative_answers' => 'learning|knowledge'
            ],
            'true_false' => [
                'question_text' => 'The speaker in the audio agrees with the main topic discussed.',
                'type' => 'true_false',
                'marks' => 5,
                'options' => [
                    ['text' => 'True', 'correct' => true],
                    ['text' => 'False', 'correct' => false]
                ]
            ],
            'listening_comprehension' => [
                'question_text' => 'What is the main idea discussed in the audio?',
                'type' => 'listening_comprehension',
                'marks' => 15,
                'comprehension_type' => 'text',
                'expected_answer' => 'Please describe the main concept explained by the speaker (this is for grading reference)'
            ]
        ];

        foreach ($quizTypes as $type) {
            if (!isset($questionTemplates[$type])) continue;

            $template = $questionTemplates[$type];

            $questionData = [
                'question_text' => $template['question_text'],
                'type' => $template['type'],
                'marks' => $template['marks']
            ];

            // Add type-specific fields
            if ($template['type'] === 'fill_blank') {
                $questionData['correct_answer'] = $template['correct_answer'];
                $questionData['alternative_answers'] = $template['alternative_answers'];
            } elseif ($template['type'] === 'listening_comprehension') {
                $questionData['comprehension_type'] = $template['comprehension_type'];
                $questionData['expected_answer'] = $template['expected_answer'];
            }

            $question = $quiz->questions()->create($questionData);

            // Create options for multiple choice and true/false
            if ($template['type'] === 'multiple_choice' && isset($template['options'])) {
                foreach ($template['options'] as $optionData) {
                    $question->options()->create([
                        'option_text' => $optionData['text'],
                        'is_correct' => $optionData['correct']
                    ]);
                }
            } elseif ($template['type'] === 'true_false' && isset($template['options'])) {
                foreach ($template['options'] as $optionData) {
                    $question->options()->create([
                        'option_text' => $optionData['text'],
                        'is_correct' => $optionData['correct']
                    ]);
                }
            }
        }

        // Update quiz total marks based on questions
        $totalMarks = $quiz->questions()->sum('marks');
        $quiz->update([
            'total_marks' => $totalMarks,
            'pass_marks' => max(1, floor($totalMarks * 0.6)) // 60% to pass
        ]);
    }

    public function destroy(Lesson $lesson, Content $content)
    {
        $this->authorize('update', $lesson->course);
        if ($content->file_path) Storage::disk('public')->delete($content->file_path);
        if ($content->quiz) $content->quiz->delete();
        $content->delete();
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

    /**
     * Create new AudioLesson from content creation
     */
    private function createAudioLesson(Request $request)
    {
        $audioFile = null;
        $audioMetadata = [];

        // Handle audio file upload
        if ($request->hasFile('audio_file')) {
            $audioFile = $request->file('audio_file')->store('audio/lessons', 'public');

            $audioMetadata = [
                'file_size' => $request->file('audio_file')->getSize(),
                'mime_type' => $request->file('audio_file')->getMimeType(),
                'original_name' => $request->file('audio_file')->getClientOriginalName(),
                'uploaded_at' => now()->toISOString()
            ];
        }

        // Create AudioLesson
        $audioLesson = \App\Models\AudioLesson::create([
            'title' => $request->input('new_lesson_title'),
            'description' => $request->input('new_lesson_description'),
            'audio_file_path' => $audioFile,
            'difficulty_level' => $request->input('audio_difficulty', 'beginner'),
            'transcript' => $request->input('audio_transcript'),
            'metadata' => array_merge($audioMetadata, [
                'category' => $request->input('new_lesson_category', 'general'),
                'created_from_course' => true
            ]),
            'is_active' => true,
            'available_for_courses' => true,
            'sort_order' => \App\Models\AudioLesson::max('sort_order') + 1
        ]);

        // Create basic audio exercises for the new lesson
        $this->createBasicAudioExercises($audioLesson);

        return $audioLesson;
    }

    /**
     * Create basic exercises for newly created AudioLesson
     */
    private function createBasicAudioExercises(\App\Models\AudioLesson $audioLesson)
    {
        // Create a basic listening comprehension exercise
        \App\Models\AudioExercise::create([
            'audio_lesson_id' => $audioLesson->id,
            'title' => 'Listening Comprehension',
            'question' => 'What is the main topic discussed in this audio?',
            'exercise_type' => 'comprehension',
            'correct_answers' => ['The main topic will be provided by the instructor'],
            'points' => 10,
            'is_active' => true,
            'sort_order' => 1
        ]);

        // Create a vocabulary exercise if relevant
        if (!empty($audioLesson->transcript)) {
            \App\Models\AudioExercise::create([
                'audio_lesson_id' => $audioLesson->id,
                'title' => 'Vocabulary Exercise',
                'question' => 'Fill in the blank based on the audio: "The speaker mentioned _____"',
                'exercise_type' => 'fill_blank',
                'correct_answers' => ['example', 'keyword'],
                'points' => 5,
                'is_active' => true,
                'sort_order' => 2
            ]);
        }
    }
}
