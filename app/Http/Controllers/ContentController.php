<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    public function show(Content $content)
    {
        $this->authorize('view', $content->lesson->course);
        $user = Auth::user();
        $user->contents()->syncWithoutDetaching($content->id);
        $course = $content->lesson->course->load(['lessons.contents' => fn($q) => $q->orderBy('order')]);
        return view('contents.show', compact('content', 'course'));
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
        // ✅ PERUBAHAN UTAMA: Validasi & Logika Penyimpanan
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
                // Untuk video, kita tidak perlu validasi body karena akan diambil dari input lain
                // Namun, kita tetap set source-nya
                $bodySource = 'body_video';
                break;
        }

        if ($request->input('type') === 'quiz') {
            $rules['quiz'] = 'required|array';
            $rules['quiz.title'] = 'required|string|max:255';
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

            if ($validated['type'] === 'quiz' && $request->has('quiz')) {
                $quiz = $this->saveQuiz($request->input('quiz'), $lesson, $content->quiz_id);
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

    // Fungsi saveQuiz tidak perlu diubah, biarkan seperti yang sudah ada
    private function saveQuiz(array $quizData, Lesson $lesson, ?int $quizId): Quiz
    {
        $quiz = Quiz::updateOrCreate(
            ['id' => $quizId],
            [
                'lesson_id' => $lesson->id, 'user_id' => Auth::id(), 'title' => $quizData['title'],
                'description' => $quizData['description'] ?? null, 'total_marks' => $quizData['total_marks'] ?? 0,
                'pass_marks' => $quizData['pass_marks'] ?? 0, 'time_limit' => $quizData['time_limit'] ?? null,
                'status' => $quizData['status'] ?? 'draft', 'show_answers_after_attempt' => filter_var($quizData['show_answers_after_attempt'] ?? false, FILTER_VALIDATE_BOOLEAN),
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
                // Logika untuk Pilihan Ganda
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
                } 
                // Logika untuk Benar/Salah
                elseif ($qData['type'] === 'true_false') {
                    // Hapus opsi lama dan buat yang baru untuk memastikan konsistensi
                    $question->options()->delete();
                    $optionTrue = $question->options()->create(['option_text' => 'True', 'is_correct' => ($qData['correct_answer_tf'] === 'true')]);
                    $optionFalse = $question->options()->create(['option_text' => 'False', 'is_correct' => ($qData['correct_answer_tf'] === 'false')]);
                    $optionIdsToKeep = [$optionTrue->id, $optionFalse->id];
                }
                // Hapus opsi yang tidak lagi digunakan untuk pertanyaan ini
                $question->options()->whereNotIn('id', $optionIdsToKeep)->delete();
            }
        }
        // Hapus pertanyaan yang tidak lagi digunakan untuk kuis ini
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
}