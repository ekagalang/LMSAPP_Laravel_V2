<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    use AuthorizesRequests;

    public function show(Content $content)
    {
        $course = $content->lesson->course;
        // Eager load relasi untuk efisiensi query
        $course->load(['lessons' => function ($query) {
            $query->orderBy('id'); // Pastikan urutan lesson benar
        }, 'lessons.contents' => function ($query) {
            $query->orderBy('id'); // Pastikan urutan content benar
        }]);

        // Buat daftar datar (flat list) dari semua konten untuk navigasi
        $allContents = $course->lessons->flatMap(function ($lesson) {
            return $lesson->contents;
        })->values();

        // Cari index dari konten yang sedang dibuka
        $currentIndex = $allContents->search(function ($item) use ($content) {
            return $item->id === $content->id;
        });

        // Tentukan konten sebelum dan sesudahnya
        $previousContent = $currentIndex > 0 ? $allContents[$currentIndex - 1] : null;
        $nextContent = $currentIndex !== false && $currentIndex < $allContents->count() - 1 ? $allContents[$currentIndex + 1] : null;

        // Tandai konten ini telah selesai oleh user
        Auth::user()->contents()->syncWithoutDetaching($content->id);

        return view('contents.show', compact('content', 'course', 'previousContent', 'nextContent'));
    }

    public function create(Lesson $lesson)
    {
        $this->authorize('update', $lesson->course);
        return view('contents.create', compact('lesson'));
    }

    public function store(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson->course);

        $rules = [
            'title' => 'required|string|max:255',
            'type' => ['required', Rule::in(['text', 'video', 'document', 'image', 'quiz', 'essay'])],
            'order' => 'nullable|integer',
        ];

        if ($request->type === 'text' || $request->type === 'video') {
            $rules['body'] = 'required|string';
        } elseif ($request->type === 'document' || $request->type === 'image') {
            $rules['file_upload'] = 'required|file|max:10240';
        } elseif ($request->type === 'quiz') {
            $rules['quiz_title'] = 'required|string|max:255';
            $rules['quiz_description'] = 'nullable|string';
            $rules['total_marks'] = 'required|integer|min:0';
            $rules['pass_marks'] = 'required|integer|min:0|lte:total_marks';
            $rules['show_answers_after_attempt'] = 'boolean';
            $rules['time_limit'] = 'nullable|integer|min:1';
            $rules['quiz_status'] = ['required', Rule::in(['draft', 'published'])];
            $rules['questions'] = 'array';
            $rules['questions.*.question_text'] = 'required|string';
            $rules['questions.*.type'] = ['required', Rule::in(['multiple_choice', 'true_false'])];
            $rules['questions.*.marks'] = 'required|integer|min:1';
            $rules['questions.*.options'] = 'array';
            $rules['questions.*.options.*.option_text'] = 'required_if:questions.*.type,multiple_choice|string';
            $rules['questions.*.options.*.is_correct'] = 'boolean';
            $rules['questions.*.correct_answer_tf'] = 'required_if:questions.*.type,true_false|in:true,false';
        }

        $validatedData = $request->validate($rules);

        $filePath = null;
        $bodyContent = null;
        $quizId = null;

        if ($request->type === 'text' || $request->type === 'video') {
            $bodyContent = $validatedData['body'];
        } elseif ($request->type === 'document' || $request->type === 'image') {
            if ($request->hasFile('file_upload')) {
                $filePath = $request->file('file_upload')->store('content_files', 'public');
            }
        } elseif ($request->type === 'quiz') {
            $quiz = Quiz::create([
                'user_id' => Auth::id(),
                'lesson_id' => $lesson->id,
                'title' => $validatedData['quiz_title'],
                'description' => $validatedData['quiz_description'],
                'total_marks' => $validatedData['total_marks'],
                'pass_marks' => $validatedData['pass_marks'],
                'show_answers_after_attempt' => $validatedData['show_answers_after_attempt'] ?? false,
                'time_limit' => $validatedData['time_limit'],
                'status' => $validatedData['quiz_status'],
            ]);

            foreach ($validatedData['questions'] as $qData) {
                $question = $quiz->questions()->create([
                    'question_text' => $qData['question_text'],
                    'type' => $qData['type'],
                    'marks' => $qData['marks'],
                ]);

                if ($qData['type'] === 'multiple_choice' && isset($qData['options'])) {
                    foreach ($qData['options'] as $optionData) {
                        $question->options()->create([
                            'option_text' => $optionData['option_text'],
                            'is_correct' => $optionData['is_correct'] ?? false,
                        ]);
                    }
                } elseif ($qData['type'] === 'true_false') {
                    $question->options()->create([
                        'option_text' => 'True',
                        'is_correct' => ($qData['correct_answer_tf'] === 'true'),
                    ]);
                    $question->options()->create([
                        'option_text' => 'False',
                        'is_correct' => ($qData['correct_answer_tf'] === 'false'),
                    ]);
                }
            }
            $quizId = $quiz->id;
        }

        $content = $lesson->contents()->create([
            'title' => $validatedData['title'],
            'type' => $validatedData['type'],
            'body' => $bodyContent,
            'file_path' => $filePath,
            'order' => $validatedData['order'] ?? $lesson->contents()->count() + 1,
            'quiz_id' => $quizId,
        ]);

        return redirect()->route('courses.show', $lesson->course)->with('success', 'Konten berhasil ditambahkan!');
    }

    public function edit(Lesson $lesson, Content $content)
    {
        $this->authorize('update', $lesson->course);
        if ($content->type === 'quiz' && $content->quiz) {
            $content->load('quiz.questions.options');
        }
        return view('contents.edit', compact('lesson', 'content'));
    }

    public function update(Request $request, Lesson $lesson, Content $content)
    {
        $this->authorize('update', $lesson->course);

        $rules = [
            'title' => 'required|string|max:255',
            'type' => ['required', Rule::in(['text', 'video', 'document', 'image', 'quiz', 'essay'])],
            'order' => 'nullable|integer',
        ];

        if ($request->type === 'text' || $request->type === 'video') {
            $rules['body'] = 'required|string';
        } elseif ($request->type === 'document' || $request->type === 'image') {
            $rules['file_upload'] = 'nullable|file|max:10240';
        } elseif ($request->type === 'quiz') {
            $rules['quiz_title'] = 'required|string|max:255';
            $rules['quiz_description'] = 'nullable|string';
            $rules['total_marks'] = 'required|integer|min:0';
            $rules['pass_marks'] = 'required|integer|min:0|lte:total_marks';
            $rules['show_answers_after_attempt'] = 'boolean';
            $rules['time_limit'] = 'nullable|integer|min:1';
            $rules['quiz_status'] = ['required', Rule::in(['draft', 'published'])];
            $rules['questions'] = 'array';
            $rules['questions.*.id'] = 'nullable|exists:questions,id';
            $rules['questions.*.question_text'] = 'required|string';
            $rules['questions.*.type'] = ['required', Rule::in(['multiple_choice', 'true_false'])];
            $rules['questions.*.marks'] = 'required|integer|min:1';
            $rules['questions.*.options_to_delete'] = 'array';
            $rules['questions.*.options_to_delete.*'] = 'exists:options,id';
            $rules['questions.*.options'] = 'array';
            $rules['questions.*.options.*.id'] = 'nullable|exists:options,id';
            $rules['questions.*.options.*.option_text'] = 'required_if:questions.*.type,multiple_choice|string';
            $rules['questions.*.options.*.is_correct'] = 'boolean';
            $rules['questions.*.correct_answer_tf'] = 'required_if:questions.*.type,true_false|in:true,false';
            $rules['questions_to_delete'] = 'array';
            $rules['questions_to_delete.*'] = 'exists:questions,id';
        }

        $validatedData = $request->validate($rules);

        $filePath = $content->file_path;
        $bodyContent = null;
        $quizId = $content->quiz_id;

        if ($request->type === 'document' || $request->type === 'image') {
            if ($request->hasFile('file_upload')) {
                if ($content->file_path) {
                    Storage::disk('public')->delete($content->file_path);
                }
                $filePath = $request->file('file_upload')->store('content_files', 'public');
            }
            $bodyContent = null;
        } elseif ($request->type === 'text' || $request->type === 'video') {
            $bodyContent = $validatedData['body'];
            $filePath = null;
        } else { // Tipe 'quiz'
            $filePath = null;
            $bodyContent = null;
        }

        if ($request->type === 'quiz') {
            $quiz = $content->quiz ?? new Quiz();

            $quiz->fill([
                'user_id' => Auth::id(),
                'lesson_id' => $lesson->id,
                'title' => $validatedData['quiz_title'],
                'description' => $validatedData['quiz_description'],
                'total_marks' => $validatedData['total_marks'],
                'pass_marks' => $validatedData['pass_marks'],
                'show_answers_after_attempt' => $validatedData['show_answers_after_attempt'] ?? false,
                'time_limit' => $validatedData['time_limit'],
                'status' => $validatedData['quiz_status'],
            ])->save();

            if (isset($validatedData['questions_to_delete'])) {
                Question::whereIn('id', $validatedData['questions_to_delete'])->delete();
            }

            if (isset($validatedData['questions'])) {
                foreach ($validatedData['questions'] as $qData) {
                    $question = $quiz->questions()->updateOrCreate(
                        ['id' => $qData['id'] ?? null],
                        [
                            'question_text' => $qData['question_text'],
                            'type' => $qData['type'],
                            'marks' => $qData['marks'],
                        ]
                    );

                    if (isset($qData['options_to_delete'])) {
                        Option::whereIn('id', $qData['options_to_delete'])->delete();
                    }

                    if ($qData['type'] === 'multiple_choice' && isset($qData['options'])) {
                        $existingOptionIds = collect($qData['options'])->pluck('id')->filter()->all();
                        $question->options()->whereNotIn('id', $existingOptionIds)->delete();

                        foreach ($qData['options'] as $optionData) {
                            $question->options()->updateOrCreate(
                                ['id' => $optionData['id'] ?? null],
                                [
                                    'option_text' => $optionData['option_text'],
                                    'is_correct' => $optionData['is_correct'] ?? false,
                                ]
                            );
                        }
                    } elseif ($qData['type'] === 'true_false') {
                        $question->options()->delete();
                        $question->options()->create([
                            'option_text' => 'True',
                            'is_correct' => ($qData['correct_answer_tf'] === 'true'),
                        ]);
                        $question->options()->create([
                            'option_text' => 'False',
                            'is_correct' => ($qData['correct_answer_tf'] === 'false'),
                        ]);
                    }
                }
            }
            $quizId = $quiz->id;
        } else {
            if ($content->quiz) {
                $content->quiz->delete();
            }
            $quizId = null;
        }

        $content->update([
            'title' => $validatedData['title'],
            'type' => $validatedData['type'],
            'body' => $bodyContent,
            'file_path' => $filePath,
            'order' => $validatedData['order'] ?? $content->order,
            'quiz_id' => $quizId,
        ]);

        return redirect()->route('courses.show', $lesson->course)->with('success', 'Konten berhasil diperbarui!');
    }

    public function destroy(Lesson $lesson, Content $content)
    {
        $this->authorize('update', $lesson->course);

        if ($content->file_path) {
            Storage::disk('public')->delete($content->file_path);
        }

        if ($content->type === 'quiz' && $content->quiz) {
            $content->quiz->delete();
        }

        $content->delete();
        return redirect()->route('courses.show', $lesson->course)->with('success', 'Konten berhasil dihapus!');
    }
}