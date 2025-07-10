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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(['text', 'video', 'document', 'image', 'quiz', 'essay'])],
            'order' => 'nullable|integer',
            'body' => 'nullable|string',
            'file_upload' => 'nullable|file|max:10240',
            'quiz' => 'nullable|array',
            'quiz.title' => 'required_if:type,quiz|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $content->lesson_id = $lesson->id;
            $content->fill($validated);

            if ($request->hasFile('file_upload')) {
                if ($content->file_path) Storage::disk('public')->delete($content->file_path);
                $content->file_path = $request->file('file_upload')->store('content_files', 'public');
            }

            if ($validated['type'] === 'quiz' && isset($validated['quiz'])) {
                $quiz = $this->saveQuiz($validated['quiz'], $lesson, $content->quiz_id);
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
                'show_answers_after_attempt' => $quizData['show_answers_after_attempt'] ?? false,
            ]
        );

        $questionIds = [];
        if (!empty($quizData['questions'])) {
            foreach ($quizData['questions'] as $qData) {
                if (empty($qData['question_text'])) continue;

                $question = Question::updateOrCreate(
                    ['id' => $qData['id'] ?? null],
                    ['quiz_id' => $quiz->id, 'question_text' => $qData['question_text'], 'type' => $qData['type'], 'marks' => $qData['marks']]
                );
                $questionIds[] = $question->id;

                $optionIds = [];
                if ($qData['type'] === 'multiple_choice' && !empty($qData['options'])) {
                    foreach ($qData['options'] as $oData) {
                        if (empty($oData['option_text'])) continue;
                        $option = Option::updateOrCreate(
                            ['id' => $oData['id'] ?? null],
                            ['question_id' => $question->id, 'option_text' => $oData['option_text'], 'is_correct' => $oData['is_correct'] ?? false]
                        );
                        $optionIds[] = $option->id;
                    }
                }
                $question->options()->whereNotIn('id', $optionIds)->delete();
            }
        }
        $quiz->questions()->whereNotIn('id', $questionIds)->delete();

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