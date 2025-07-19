<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    public function show(Content $content)
    {
        $user = Auth::user();
        $course = $content->lesson->course;

        // Otorisasi dasar: pastikan pengguna terdaftar di kursus
        $this->authorize('view', $course);

        // ✅ PERBAIKAN: Ambil semua konten dalam urutan yang benar dengan method yang diperbaiki
        $orderedContents = $this->getOrderedContents($course);

        // Cek apakah konten yang diminta sudah terbuka (unlocked)
        $unlockedContents = $this->getUnlockedContents($user, $orderedContents);

        if (!$unlockedContents->contains('id', $content->id) && !$user->hasRole(['super-admin', 'instructor'])) {
            // Jika konten terkunci (dan pengguna bukan admin/instruktur), kembalikan dengan pesan error.
            return redirect()->back()->with('error', 'Anda harus menyelesaikan materi sebelumnya terlebih dahulu.');
        }

        // Jika lolos, kirim data yang diperlukan ke view
        $content->load('lesson.course', 'discussions.user', 'discussions.replies.user');

        // Untuk admin/instruktur, semua konten dianggap terbuka
        if ($user->hasRole(['super-admin', 'instructor'])) {
            $unlockedContents = $orderedContents;
        }

        return view('contents.show', compact('content', 'course', 'unlockedContents'));
    }

    public function completeAndContinue(Request $request, Content $content)
    {
        $user = Auth::user();
        $course = $content->lesson->course;

        // Tandai konten saat ini sebagai selesai
        $user->completedContents()->syncWithoutDetaching($content->id);

        // ✅ PERBAIKAN: Cari konten berikutnya dengan logic yang diperbaiki
        $orderedContents = $this->getOrderedContents($course);
        $currentIndex = $orderedContents->search(function($item) use ($content) {
            return $item->id === $content->id;
        });

        $nextContent = null;
        if ($currentIndex !== false && ($currentIndex + 1) < $orderedContents->count()) {
            $nextContent = $orderedContents[$currentIndex + 1];
        }

        // Jika ada materi selanjutnya, arahkan ke sana.
        if ($nextContent) {
            return redirect()->route('contents.show', $nextContent->id)->with('success', 'Materi selesai! Lanjut ke materi berikutnya.');
        }

        // Jika tidak ada, berarti kursus selesai.
        return redirect()->route('courses.show', $course->id)->with('success', 'Selamat! Anda telah menyelesaikan semua materi di kursus ini.');
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

        $completedContentIds = $user->completedContents()->pluck('content_id')->toArray();
        $unlocked = collect();
        $shouldUnlock = true; // Konten pertama selalu terbuka

        foreach ($orderedContents as $content) {
            if ($shouldUnlock) {
                $unlocked->push($content);

                // Cek apakah konten ini sudah diselesaikan untuk menentukan apakah konten berikutnya terbuka
                if ($content->type === 'quiz' && $content->quiz_id) {
                    // Untuk kuis, cek apakah sudah lulus
                    $shouldUnlock = $user->quizAttempts()
                        ->where('quiz_id', $content->quiz_id)
                        ->where('passed', true)
                        ->exists();
                } elseif ($content->type === 'essay') {
                    // Untuk esai, cek apakah sudah dinilai
                    $shouldUnlock = $user->essaySubmissions()
                        ->where('content_id', $content->id)
                        ->whereNotNull('graded_at')
                        ->exists();
                } else {
                    // Untuk konten biasa, cek di tabel completed_contents
                    $shouldUnlock = in_array($content->id, $completedContentIds);
                }
            } else {
                // Begitu ada satu konten yang belum diselesaikan, hentikan
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
            'type' => ['required', Rule::in(['text', 'video', 'document', 'image', 'quiz', 'essay'])],
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

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $content->lesson_id = $lesson->id;
            $content->fill($validated);

            // Secara eksplisit atur kolom 'body' dari sumber yang benar
            if ($bodySource) {
                $content->body = $request->input($bodySource);
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
                'lesson_id' => $lesson->id, 'user_id' => Auth::id(), 'title' => $quizData['title'],
                'description' => $quizData['description'] ?? null, 'total_marks' => $quizData['total_marks'] ?? 0,
                'pass_marks' => $quizData['pass_marks'] ?? 0, 'time_limit' => $quizData['time_limit'] ?? null,
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
}
