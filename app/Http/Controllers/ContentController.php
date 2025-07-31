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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\CertificateController;

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

        $content->load('lesson.course', 'discussions.user', 'discussions.replies.user', 'quiz.questions.options');

        if ($user->hasRole(['super-admin', 'instructor'])) {
            $unlockedContents = $orderedContents;
        }

        // =================================================================
        // PERBAIKAN: Hapus blok ini untuk mencegah konten selesai otomatis saat dikunjungi
        /*
        if ($user->hasRole('participant') && !in_array($content->type, ['quiz', 'essay'])) {
            $user->completedContents()->syncWithoutDetaching([
                $content->id => [
                    'completed' => true,
                    'completed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }
        */
        // =================================================================

        $hasPassedQuizBefore = false;
        if ($content->type === 'quiz' && $content->quiz) {
            if ($user) {
                $hasPassedQuizBefore = $user->quizAttempts()
                                            ->where('quiz_id', $content->quiz->id)
                                            ->where('passed', true)
                                            ->exists();
            }
        }

        // =================================================================
        // PERBAIKAN: Kirim juga $orderedContents ke view
        return view('contents.show', compact('content', 'course', 'unlockedContents', 'hasPassedQuizBefore', 'orderedContents'));
        // =================================================================
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

        // Cek progress course
        $progressData = $user->getProgressForCourse($course);

        // Jika progres sudah 100% atau lebih
        if ($progressData['progress_percentage'] >= 100) {

            // PERBAIKAN: Gunakan method internal bukan CertificateController::generateForUser
            $this->checkAndGenerateCertificate($course, $user);

            // Tandai kursus sebagai selesai untuk pengguna
            $user->courses()->updateExistingPivot($course->id, ['completed_at' => now()]);

            return redirect()->route('dashboard')->with([
                'success' => '🎉 Selamat! Anda telah menyelesaikan kursus "' . $course->title . '" dengan sukses!',
                'course_completed' => true,
                'completed_course' => $course->title
            ]);
        }

        // Lanjutkan ke konten berikutnya atau kembali ke course
        $nextContent = $lesson->contents()
            ->where('order', '>', $content->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextContent) {
            return redirect()->route('contents.show', ['content' => $nextContent->id])
                ->with('success', 'Lanjut ke konten berikutnya!');
        }

        return redirect()->route('courses.show', $course->id)
            ->with('success', 'Selamat! Anda telah menyelesaikan konten ini.');
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
    }

    // ✅ PERBAIKAN: Method untuk mendapatkan konten yang sudah terbuka
    private function getUnlockedContents(User $user, $orderedContents)
    {
        // Jika user adalah admin/instruktur, semua konten terbuka
        if ($user->hasRole(['super-admin', 'instructor'])) {
            return $orderedContents;
        }

        $unlocked = collect();

        foreach ($orderedContents as $index => $content) {
            // Konten pertama selalu terbuka
            if ($index === 0) {
                $unlocked->push($content);
                continue;
            }

            // Untuk konten selanjutnya, cek apakah konten sebelumnya sudah diselesaikan
            $previousContent = $orderedContents[$index - 1];
            $previousCompleted = false;

            if ($previousContent->type === 'quiz' && $previousContent->quiz_id) {
                // Untuk kuis, cek apakah sudah lulus
                $previousCompleted = $user->quizAttempts()
                    ->where('quiz_id', $previousContent->quiz_id)
                    ->where('passed', true)
                    ->exists();
            } elseif ($previousContent->type === 'essay') {
                // ✅ PERUBAHAN: Untuk esai, cek apakah sudah submit
                $previousCompleted = $user->essaySubmissions()
                    ->where('content_id', $previousContent->id)
                    ->exists();
            } else {
                // Untuk konten biasa, cek di tabel completed
                $previousCompleted = $user->completedContents()
                    ->where('content_id', $previousContent->id)
                    ->exists();
            }

            // Jika konten sebelumnya sudah selesai, buka konten ini
            if ($previousCompleted) {
                $unlocked->push($content);
            } else {
                // Jika konten sebelumnya belum selesai, stop di sini
                break;
            }
        }

        return $unlocked;
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
        $content->load(['quiz.questions.options']);
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
            'type' => ['required', Rule::in(['text', 'video', 'document', 'image', 'quiz', 'essay', 'zoom'])],
            'order' => 'nullable|integer',
            'file_upload' => 'nullable|file|max:10240',
        ];

        // Tentukan aturan validasi dan sumber data 'body' berdasarkan tipe konten
        $bodySource = null;
        switch ($request->input('type')) {
            case 'text':
            case 'essay':
                $rules['body_text'] = 'nullable|string';
                $bodySource = 'body_text';
                break;
            case 'video':
                $rules['body_video'] = 'nullable|url';
                $bodySource = 'body_video';
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
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $content->lesson_id = $lesson->id;
            $content->fill($validated);

            // Secara eksplisit atur kolom 'body' dari sumber yang benar
            if ($bodySource) {
                $content->body = $request->input($bodySource);
            } elseif ($validated['type'] === 'zoom') {
                $content->body = json_encode([
                    'link' => $validated['zoom_link'],
                    'meeting_id' => $validated['zoom_meeting_id'],
                    'password' => $validated['zoom_password'] ?? '',
                ]);
            } else {
                $content->body = null;
            }

            if ($request->hasFile('file_upload')) {
                if ($content->file_path) Storage::disk('public')->delete($content->file_path);
                $content->file_path = $request->file('file_upload')->store('content_files', 'public');
            }

            // ✅ PERBAIKAN: Set order dengan lebih hati-hati
            if (!$content->exists) {
                // Untuk content baru, ambil order terakhir dalam lesson ini
                $lastOrder = $lesson->contents()->max('order') ?? 0;
                $content->order = $lastOrder + 1;
            } else {
                // Untuk content yang sudah ada, gunakan order dari request atau yang sudah ada
                $content->order = $request->input('order', $content->order ?? 1);
            }

            if ($validated['type'] === 'quiz' && $request->has('quiz')) {
                $quizData = $request->input('quiz');
                $quizData['time_limit'] = $validated['time_limit'] ?? null;
                $quiz = $this->saveQuiz($quizData, $lesson, $content->quiz_id);
                $content->quiz_id = $quiz->id;
            } else {
                if ($content->quiz) $content->quiz->delete();
                $content->quiz_id = null;
            }

            $content->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('courses.show', $lesson->course)->with('success', 'Konten berhasil disimpan.');
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
}
