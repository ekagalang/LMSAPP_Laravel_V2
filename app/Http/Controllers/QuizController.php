<?php

namespace App\Http\Controllers;

use App\Models\Content; // Diperlukan jika ada interaksi dengan model Content
use App\Models\Lesson;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\QuizAttempt;
use App\Models\QuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class QuizController extends Controller // Pastikan nama kelas ini BENAR: QuizController
{
    use AuthorizesRequests;

    public function __construct()
    {
        // Pastikan konstruktor ini ada dan tidak dikomentari
        $this->middleware('can:manage-courses')->except(['show', 'startAttempt', 'submitAttempt', 'showResult']);
    }

    /**
     * Display a listing of the resource.
     * Biasanya untuk daftar kuis (admin/instruktur).
     */
    public function index()
    {
        $this->authorize('viewAny', Quiz::class);

        $user = Auth::user();
        if ($user->isAdmin()) {
            $quizzes = Quiz::with('instructor', 'lesson.course')->latest()->get();
        } elseif ($user->isInstructor()) {
            $quizzes = Quiz::where('user_id', $user->id)->with('instructor', 'lesson.course')->latest()->get();
        } else {
            // Partisipan tidak seharusnya ada di sini karena middleware
            abort(403, 'Unauthorized action.');
        }

        return view('quizzes.index', compact('quizzes'));
    }

    /**
     * Display the specified quiz.
     * Menampilkan detail kuis (sebelum memulai percobaan).
     */
    public function show(Quiz $quiz)
    {
        // Pastikan relasi lesson dan course dimuat
        $quiz->load('questions.options', 'lesson.course', 'instructor');
        $this->authorize('view', $quiz);
        return view('quizzes.show', compact('quiz'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Quiz::class);
        // Anda mungkin perlu menyesuaikan pelajaran yang ditampilkan di sini
        $lessons = Lesson::whereHas('course', function ($query) {
            $query->where('user_id', Auth::id()); // Hanya pelajaran dari kursus yang dibuat instruktur ini
        })->get();
        return view('quizzes.create', compact('lessons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Quiz::class);

        $validatedData = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_marks' => 'required|integer|min:0',
            'pass_marks' => 'required|integer|min:0|lte:total_marks',
            'show_answers_after_attempt' => 'boolean',
            'time_limit' => 'nullable|integer|min:1',
            'status' => ['required', Rule::in(['draft', 'published'])],
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => ['required', Rule::in(['multiple_choice', 'true_false'])],
            'questions.*.marks' => 'required|integer|min:1',
            'questions.*.options' => 'array',
            'questions.*.options.*.option_text' => 'required_if:questions.*.type,multiple_choice|string',
            'questions.*.options.*.is_correct' => 'boolean',
            'questions.*.correct_answer_tf' => 'required_if:questions.*.type,true_false|in:true,false',
        ]);

        $quiz = Quiz::create([
            'user_id' => Auth::id(),
            'lesson_id' => $validatedData['lesson_id'],
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'total_marks' => $validatedData['total_marks'],
            'pass_marks' => $validatedData['pass_marks'],
            'show_answers_after_attempt' => $request->has('show_answers_after_attempt'),
            'time_limit' => $validatedData['time_limit'],
            'status' => $validatedData['status'],
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

        return redirect()->route('quizzes.index')->with('success', 'Kuis berhasil dibuat!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quiz $quiz)
    {
        $this->authorize('update', $quiz);
        $quiz->load('questions.options');
        $lessons = Lesson::whereHas('course', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();

        return view('quizzes.edit', compact('quiz', 'lessons'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $this->authorize('update', $quiz);

        $validatedData = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_marks' => 'required|integer|min:0',
            'pass_marks' => 'required|integer|min:0|lte:total_marks',
            'show_answers_after_attempt' => 'boolean',
            'time_limit' => 'nullable|integer|min:1',
            'status' => ['required', Rule::in(['draft', 'published'])],
            'questions_to_delete' => 'array',
            'questions_to_delete.*' => 'exists:questions,id',
            'questions' => 'array',
            'questions.*.id' => 'nullable|exists:questions,id',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => ['required', Rule::in(['multiple_choice', 'true_false'])],
            'questions.*.marks' => 'required|integer|min:1',
            'questions.*.options_to_delete' => 'array',
            'questions.*.options_to_delete.*' => 'exists:options,id',
            'questions.*.options' => 'array',
            'questions.*.options.*.id' => 'nullable|exists:options,id',
            'questions.*.options.*.option_text' => 'required_if:questions.*.type,multiple_choice|string',
            'questions.*.options.*.is_correct' => 'boolean',
            'questions.*.correct_answer_tf' => 'required_if:questions.*.type,true_false|in:true,false',
        ]);

        $quiz->update([
            'lesson_id' => $validatedData['lesson_id'],
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'total_marks' => $validatedData['total_marks'],
            'pass_marks' => $validatedData['pass_marks'],
            'show_answers_after_attempt' => $request->has('show_answers_after_attempt'),
            'time_limit' => $validatedData['time_limit'],
            'status' => $validatedData['status'],
        ]);

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

        return redirect()->route('quizzes.index')->with('success', 'Kuis berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        $this->authorize('delete', $quiz);
        $quiz->delete();
        return redirect()->route('quizzes.index')->with('success', 'Kuis berhasil dihapus!');
    }

    /**
     * Start a new quiz attempt.
     */
    public function startAttempt(Quiz $quiz)
    {
        // Add this line to eagerly load the lesson and course relationships
        $quiz->load('lesson.course'); //

        // --- Tambahkan baris ini untuk debugging ---
        // ($quiz);
        // ------------------------------------------

        $user = Auth::user();

        // Pastikan user adalah peserta
        if (!$user->isParticipant()) {
            abort(403, 'Anda tidak memiliki izin untuk mengerjakan kuis.');
        }

        // Pastikan kuis sudah dipublikasikan
        if ($quiz->status !== 'published') {
            return redirect()->back()->with('error', 'Kuis ini belum dipublikasikan.');
        }

        // Pastikan user terdaftar di kursus induk kuis
        // Asumsi Quiz memiliki relasi lesson, dan lesson memiliki relasi course
        if (!$quiz->lesson || !$quiz->lesson->course || !$user->isEnrolled($quiz->lesson->course)) {
            return redirect()->back()->with('error', 'Anda harus terdaftar di kursus ini untuk memulai kuis.');
        }

        // Buat percobaan kuis baru
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'started_at' => now(),
        ]);

        return view('quizzes.attempt', compact('quiz', 'attempt'));
    }

    /**
     * Submit a quiz attempt.
     */
    public function submitAttempt(Request $request, Quiz $quiz, QuizAttempt $attempt)
    {
        $user = Auth::user();

        // Pastikan user adalah pemilik attempt
        if ($attempt->user_id !== $user->id || $attempt->quiz_id !== $quiz->id) {
            abort(403, 'Akses ditolak.');
        }

        // Pastikan attempt belum selesai
        if ($attempt->completed_at) {
            return redirect()->route('quizzes.result', [$quiz, $attempt])->with('info', 'Anda sudah menyelesaikan percobaan ini.');
        }

        $rules = [
            'answers' => 'array|required',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.option_id' => 'nullable|exists:options,id', // Untuk multiple_choice
            'answers.*.answer_text' => 'nullable|string|in:True,False', // Untuk true_false
        ];

        $validatedData = $request->validate($rules);

        $score = 0;
        foreach ($validatedData['answers'] as $answerData) {
            $question = Question::find($answerData['question_id']);
            if (!$question) continue;

            $isCorrect = false;
            if ($question->type === 'multiple_choice') {
                if (isset($answerData['option_id'])) {
                    $selectedOption = Option::find($answerData['option_id']);
                    if ($selectedOption && $selectedOption->question_id === $question->id && $selectedOption->is_correct) {
                        $isCorrect = true;
                        $score += $question->marks;
                    }
                }
            } elseif ($question->type === 'true_false') {
                if (isset($answerData['answer_text'])) {
                    $correctOption = $question->options->where('is_correct', true)->first();
                    if ($correctOption && Str::lower($correctOption->option_text) === Str::lower($answerData['answer_text'])) {
                        $isCorrect = true;
                        $score += $question->marks;
                    }
                }
            }

            // Simpan jawaban user
            QuestionAnswer::create([
                'quiz_attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'option_id' => $answerData['option_id'] ?? null,
                'answer_text' => $answerData['answer_text'] ?? null,
                'is_correct' => $isCorrect,
            ]);
        }

        // Update attempt dengan skor dan status lulus
        $attempt->score = $score;
        $attempt->passed = ($score >= $quiz->pass_marks);
        $attempt->completed_at = now();
        $attempt->save();

        return redirect()->route('quizzes.result', [$quiz, $attempt])->with('success', 'Kuis berhasil diselesaikan!');
    }

    /**
     * Show the quiz result.
     */
    public function showResult(Quiz $quiz, QuizAttempt $attempt)
    {
        // Pastikan user adalah pemilik attempt
        if ($attempt->user_id !== Auth::id() || $attempt->quiz_id !== $quiz->id) {
            abort(403, 'Akses ditolak.');
        }

        // Pastikan attempt sudah selesai
        if (!$attempt->completed_at) {
            return redirect()->route('quizzes.start_attempt', $quiz)->with('error', 'Percobaan kuis belum selesai.');
        }

        $attempt->load('answers.question.options'); // Muat relasi untuk menampilkan detail jawaban

        return view('quizzes.result', compact('quiz', 'attempt'));
    }

    /**
     * Get quiz question form partial for AJAX.
     * Metode ini digunakan oleh JavaScript untuk memuat form pertanyaan kuis secara dinamis.
     * Ini bukan bagian dari resource controller standar.
     */
    public function getQuestionFormPartial(Request $request)
    {
        $question_loop_index = $request->query('index');
        // $is_new = $request->query('is_new'); // Jika diperlukan untuk membedakan new/edit
        return view('quizzes.partials.question-form-fields', compact('question_loop_index'));
    }

    /**
     * Get full quiz form partial for AJAX.
     * Metode ini digunakan oleh JavaScript untuk memuat seluruh form kuis secara dinamis (detail dan pertanyaan).
     */
    public function getFullQuizFormPartial()
    {
        return view('quizzes.partials.full-quiz-form');
    }
}