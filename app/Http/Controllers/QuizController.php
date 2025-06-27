<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\Quiz; // Tambahkan ini
use App\Models\Question; // Tambahkan ini
use App\Models\Option; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str; // Untuk True/False di kuis

class ContentController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        // Middleware ini tetap dipertahankan
        $this->middleware('can:manage-courses')->except(['show']);
    }

    /**
     * Display the specified content (for participants).
     */
    public function show(Lesson $lesson, Content $content)
    {
        $course = $lesson->course;

        // Jika tipe konten adalah kuis, muat relasi kuis dan pertanyaan/opsi
        if ($content->type === 'quiz') {
            $content->load('quiz.questions.options');
            // Otorisasi tambahan untuk kuis, seperti memastikan user enroll kursus
            if (Auth::user()->isParticipant() && !$content->lesson->course->participants->contains(Auth::id())) {
                abort(403, 'Anda belum terdaftar di kursus ini untuk melihat kuis.');
            }
            // Jika kuis belum dipublikasikan, mungkin tidak bisa dilihat oleh peserta
            if ($content->quiz && $content->quiz->status !== 'published' && !Auth::user()->isAdmin() && !Auth::user()->isInstructor()) {
                 abort(403, 'Kuis ini belum tersedia.');
            }
        }
        return view('contents.show', compact('lesson', 'content', 'course'));
    }

    /**
     * Show the form for creating a new content for a specific lesson.
     */
    public function create(Lesson $lesson)
    {
        $this->authorize('update', $lesson->course);
        // Kita perlu pass data quiz yang sudah ada jika ingin user memilih quiz yang sudah dibuat
        // Atau user akan selalu membuat quiz baru dari sini
        // Untuk kesederhanaan, kita akan selalu membuat kuis baru jika tipe 'quiz' dipilih
        return view('contents.create', compact('lesson'));
    }

    /**
     * Store a newly created content in storage.
     */
    public function store(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson->course);

        $rules = [
            'title' => 'required|string|max:255',
            'type' => ['required', Rule::in(['text', 'video', 'document', 'image', 'quiz'])], // Tambahkan 'quiz'
            'order' => 'nullable|integer',
        ];

        if ($request->type === 'text' || $request->type === 'video') {
            $rules['body'] = 'required|string';
        } elseif ($request->type === 'document' || $request->type === 'image') {
            $rules['file_upload'] = 'required|file|max:10240';
        } elseif ($request->type === 'quiz') {
            // Validasi untuk data kuis jika tipenya 'quiz'
            $rules['quiz_title'] = 'required|string|max:255';
            $rules['quiz_description'] = 'nullable|string';
            $rules['total_marks'] = 'required|integer|min:0';
            $rules['pass_marks'] = 'required|integer|min:0|lte:total_marks';
            $rules['show_answers_after_attempt'] = 'boolean';
            $rules['time_limit'] = 'nullable|integer|min:1';
            $rules['quiz_status'] = ['required', Rule::in(['draft', 'published'])];
            $rules['questions'] = 'array'; // Array pertanyaan
            $rules['questions.*.question_text'] = 'required|string';
            $rules['questions.*.type'] = ['required', Rule::in(['multiple_choice', 'true_false'])];
            $rules['questions.*.marks'] = 'required|integer|min:1';
            $rules['questions.*.options'] = 'array'; // Array opsi untuk multiple_choice
            $rules['questions.*.options.*.option_text'] = 'required_if:questions.*.type,multiple_choice|string';
            $rules['questions.*.options.*.is_correct'] = 'boolean';
            // Untuk true_false
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
            // Logika untuk membuat kuis baru
            $quiz = Quiz::create([
                'user_id' => Auth::id(),
                'lesson_id' => $lesson->id, // Kuis terhubung langsung ke pelajaran yang menjadi induk konten ini
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
            $quizId = $quiz->id; // Ambil ID kuis yang baru dibuat
        }

        $content = $lesson->contents()->create([
            'title' => $validatedData['title'],
            'type' => $validatedData['type'],
            'body' => $bodyContent,
            'file_path' => $filePath,
            'order' => $validatedData['order'] ?? $lesson->contents()->count() + 1,
            'quiz_id' => $quizId, // Simpan ID kuis jika ada
        ]);

        return redirect()->route('courses.show', $lesson->course)->with('success', 'Konten berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified content.
     */
    public function edit(Lesson $lesson, Content $content)
    {
        $this->authorize('update', $lesson->course);
        // Jika tipe konten adalah kuis, muat juga data kuisnya
        if ($content->type === 'quiz' && $content->quiz) {
            $content->load('quiz.questions.options');
        }
        return view('contents.edit', compact('lesson', 'content'));
    }

    /**
     * Update the specified content in storage.
     */
    public function update(Request $request, Lesson $lesson, Content $content)
    {
        $this->authorize('update', $lesson->course);

        $rules = [
            'title' => 'required|string|max:255',
            'type' => ['required', Rule::in(['text', 'video', 'document', 'image', 'quiz'])], // Tambahkan 'quiz'
            'order' => 'nullable|integer',
        ];

        if ($request->type === 'text' || $request->type === 'video') {
            $rules['body'] = 'required|string';
        } elseif ($request->type === 'document' || $request->type === 'image') {
            $rules['file_upload'] = 'nullable|file|max:10240'; // Nullable karena mungkin tidak upload baru
        } elseif ($request->type === 'quiz') {
            // Validasi untuk data kuis jika tipenya 'quiz'
            $rules['quiz_title'] = 'required|string|max:255';
            $rules['quiz_description'] = 'nullable|string';
            $rules['total_marks'] = 'required|integer|min:0';
            $rules['pass_marks'] = 'required|integer|min:0|lte:total_marks';
            $rules['show_answers_after_attempt'] = 'boolean';
            $rules['time_limit'] = 'nullable|integer|min:1';
            $rules['quiz_status'] = ['required', Rule::in(['draft', 'published'])];
            $rules['questions'] = 'array';
            $rules['questions.*.id'] = 'nullable|exists:questions,id'; // Untuk pertanyaan yang sudah ada
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

        $filePath = $content->file_path; // Pertahankan file lama by default
        $bodyContent = null;
        $quizId = $content->quiz_id;

        // Jika tipe berubah atau file baru diupload
        if ($request->type === 'document' || $request->type === 'image') {
            if ($request->hasFile('file_upload')) {
                if ($content->file_path) {
                    Storage::disk('public')->delete($content->file_path);
                }
                $filePath = $request->file('file_upload')->store('content_files', 'public');
            }
            $bodyContent = null; // Pastikan body kosong jika ada file
        } elseif ($request->type === 'text' || $request->type === 'video') {
            $bodyContent = $validatedData['body'];
            $filePath = null; // Pastikan file_path kosong jika tipe teks/video
        } else { // Tipe 'quiz'
            $filePath = null;
            $bodyContent = null;
        }

        // Logika Update/Create Kuis jika tipe kontennya adalah 'quiz'
        if ($request->type === 'quiz') {
            // Jika kuis belum ada, buat baru
            $quiz = $content->quiz ?? new Quiz();

            $quiz->fill([
                'user_id' => Auth::id(),
                'lesson_id' => $lesson->id, // Kuis selalu terhubung ke lesson induk konten
                'title' => $validatedData['quiz_title'],
                'description' => $validatedData['quiz_description'],
                'total_marks' => $validatedData['total_marks'],
                'pass_marks' => $validatedData['pass_marks'],
                'show_answers_after_attempt' => $validatedData['show_answers_after_attempt'] ?? false,
                'time_limit' => $validatedData['time_limit'],
                'status' => $validatedData['quiz_status'],
            ])->save(); // Save quiz untuk mendapatkan ID jika baru dibuat

            // Hapus pertanyaan yang ditandai untuk dihapus
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
                        // Hapus opsi lama jika tidak ada di request dan tipenya multiple_choice (untuk memastikan tidak ada opsi sisa dari True/False sebelumnya)
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
                        // Hapus semua opsi lama sebelum membuat ulang True/False
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
            $quizId = $quiz->id; // Dapatkan ID kuis yang diupdate/dibuat
        } else {
            // Jika tipe konten BUKAN kuis, dan sebelumnya ada kuis terkait, hapus kuis tersebut
            if ($content->quiz) {
                $content->quiz->delete(); // Ini akan menghapus kuis beserta pertanyaan/opsi
            }
            $quizId = null;
        }

        $content->update([
            'title' => $validatedData['title'],
            'type' => $validatedData['type'],
            'body' => $bodyContent,
            'file_path' => $filePath,
            'order' => $validatedData['order'] ?? $content->order,
            'quiz_id' => $quizId, // Update ID kuis
        ]);

        return redirect()->route('courses.show', $lesson->course)->with('success', 'Konten berhasil diperbarui!');
    }


    /**
     * Remove the specified content from storage.
     */
    public function destroy(Lesson $lesson, Content $content)
    {
        $this->authorize('update', $lesson->course);

        // Hapus file terkait jika ada
        if ($content->file_path) {
            Storage::disk('public')->delete($content->file_path);
        }

        // Jika konten adalah kuis, hapus juga kuis terkait
        if ($content->type === 'quiz' && $content->quiz) {
            $content->quiz->delete();
        }

        $content->delete();
        return redirect()->route('courses.show', $lesson->course)->with('success', 'Konten berhasil dihapus!');
    }
}