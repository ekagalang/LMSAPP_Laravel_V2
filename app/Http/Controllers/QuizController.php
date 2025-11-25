<?php

namespace App\Http\Controllers;

use App\Models\Content;
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
use Carbon\Carbon;

class QuizController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('can:manage own courses')->except([
            'show',
            'start',
            'startAttempt',
            'submitAttempt',
            'showResult',
            'checkTimeRemaining',
            'saveProgress',
            'leaderboard',
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $this->authorize('viewAny', Quiz::class);

        if ($user->can('manage all courses')) {
            $quizzes = Quiz::with('instructor', 'lesson.course')->latest()->get();
        } elseif ($user->can('view quizzes')) {
            $quizzes = Quiz::where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('lesson.course.instructors', function ($sub) use ($user) {
                          $sub->where('user_id', $user->id);
                      });
                })
                ->with('instructor', 'lesson.course')
                ->latest()
                ->get();
        } else {
            abort(403, 'Unauthorized action.');
        }

        return view('quizzes.index', compact('quizzes'));
    }

    /**
     * Display the specified quiz.
     */
    public function show(Quiz $quiz)
    {
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
        $lessons = Lesson::whereHas('course', function ($query) {
            $query->where('user_id', Auth::id());
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
            'passing_percentage' => 'required|integer|min:0|max:100',
            'show_answers_after_attempt' => 'boolean',
            'enable_leaderboard' => 'boolean',
            'time_limit' => 'nullable|integer|min:1|max:1440',
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
            'passing_percentage' => $validatedData['passing_percentage'],
            'show_answers_after_attempt' => $request->has('show_answers_after_attempt'),
            'enable_leaderboard' => $request->has('enable_leaderboard'),
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
            'passing_percentage' => 'required|integer|min:0|max:100',
            'show_answers_after_attempt' => 'boolean',
            'enable_leaderboard' => 'boolean',
            'time_limit' => 'nullable|integer|min:1|max:1440',
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
            'passing_percentage' => $validatedData['passing_percentage'],
            'show_answers_after_attempt' => $request->has('show_answers_after_attempt'),
            'enable_leaderboard' => $request->has('enable_leaderboard'),
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
        $quiz->load('lesson.course');
        $user = Auth::user();

        // Bypass for users with full course management
        $canBypass = $user->can('manage all courses');

        if (!$canBypass && !$user->can('attempt quizzes')) {
            abort(403, 'Anda tidak memiliki izin untuk mengerjakan kuis.');
        }

        // =================================================================
        // PERUBAHAN DIMULAI DI SINI
        // =================================================================
        // Cek apakah pengguna sudah pernah lulus kuis ini dari percobaan sebelumnya.
        // Super-admin bisa mengerjakan ulang untuk testing
        if (!$canBypass) {
            $hasPassedQuizBefore = $user->quizAttempts()
                                        ->where('quiz_id', $quiz->id)
                                        ->where('passed', true)
                                        ->exists();

            // Jika sudah pernah lulus, blokir dan arahkan ke halaman hasil.
            if ($hasPassedQuizBefore) {
                $lastPassedAttempt = $user->quizAttempts()
                                          ->where('quiz_id', $quiz->id)
                                          ->where('passed', true)
                                          ->latest('completed_at')
                                          ->first();

                return redirect()->route('quizzes.result', ['quiz' => $quiz, 'attempt' => $lastPassedAttempt])
                       ->with('info', 'Anda sudah lulus kuis ini dan tidak dapat mengerjakannya kembali.');
            }
        }
        // =================================================================
        // PERUBAHAN SELESAI DI SINI
        // =================================================================

        // ✅ DEBUG: Log quiz access attempt
        \Log::info('Quiz access attempt', [
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'quiz_status' => $quiz->status,
            'quiz_lesson_id' => $quiz->lesson_id,
            'lesson_id' => $quiz->lesson->id ?? 'null',
            'lesson_course_id' => $quiz->lesson->course_id ?? 'null',
            'course_id' => $quiz->lesson->course->id ?? 'null',
            'is_enrolled' => $quiz->lesson && $quiz->lesson->course ? $user->isEnrolled($quiz->lesson->course) : false,
        ]);

        if ($quiz->status !== 'published') {
            \Log::warning('Quiz access denied - not published', [
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'status' => $quiz->status,
            ]);
            return redirect()->back()->with('error', 'Kuis ini belum dipublikasikan.');
        }

        // Managers bypass enrollment check
        if (!$canBypass && (!$quiz->lesson || !$quiz->lesson->course || !$user->isEnrolled($quiz->lesson->course))) {
            \Log::warning('Quiz access denied - not enrolled or missing lesson/course', [
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'has_lesson' => !is_null($quiz->lesson),
                'has_course' => $quiz->lesson ? !is_null($quiz->lesson->course) : false,
                'is_enrolled' => $quiz->lesson && $quiz->lesson->course ? $user->isEnrolled($quiz->lesson->course) : false,
            ]);
            return redirect()->back()->with('error', 'Anda harus terdaftar di kursus ini untuk memulai kuis.');
        }

        $attempt = QuizAttempt::where([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'completed_at' => null,
        ])->first();

        if ($attempt) {
            if (is_null($attempt->started_at)) {
                $attempt->started_at = now();
                $attempt->save();
            }
        } else {
            $attempt = QuizAttempt::create([
                'quiz_id' => $quiz->id,
                'user_id' => $user->id,
                'started_at' => now(),
            ]);
        }

        if (is_null($attempt->started_at)) {
            $attempt->started_at = now();
            $attempt->save();
        }

        $timeRemaining = null;
        if ($quiz->time_limit && $attempt->started_at) {
            $startedAt = $attempt->started_at->copy();
            $deadline = $startedAt->addMinutes($quiz->time_limit);
            $now = now();

            if ($now->gte($deadline)) {
                $this->autoSubmitAttempt($quiz, $attempt);
                return redirect()->route('quizzes.result', ['quiz' => $quiz, 'attempt' => $attempt])
                    ->with('warning', 'Waktu kuis telah habis. Jawaban yang sudah terisi akan dinilai.');
            }

            $timeRemaining = (int) $now->diffInSeconds($deadline, false);
        }

        return view('quizzes.attempt', compact('quiz', 'attempt', 'timeRemaining'));
    }

    /**
     * Save progress for auto-save
     */
    public function saveProgress(Request $request, Quiz $quiz, QuizAttempt $attempt)
    {
        $user = Auth::user();

        if ($attempt->user_id !== $user->id || $attempt->quiz_id !== $quiz->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($attempt->completed_at) {
            return response()->json(['error' => 'Quiz already completed'], 400);
        }

        if (is_null($attempt->started_at)) {
            $attempt->started_at = now();
            $attempt->save();
        }

        if ($quiz->time_limit) {
            $startedAt = $attempt->started_at->copy();
            $deadline = $startedAt->addMinutes($quiz->time_limit);
            if (now() > $deadline) {
                $this->autoSubmitAttempt($quiz, $attempt);
                return response()->json(['expired' => true]);
            }
        }

        $answers = $request->input('answers', []);
        session()->put("quiz_progress_{$quiz->id}_{$user->id}", $answers);

        return response()->json(['success' => true]);
    }

    /**
     * Check time remaining for quiz attempt
     */
    public function checkTimeRemaining(Quiz $quiz, QuizAttempt $attempt)
    {
        $user = Auth::user();

        if ($attempt->user_id !== $user->id || $attempt->quiz_id !== $quiz->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($attempt->completed_at) {
            return response()->json(['completed' => true]);
        }

        if (!$quiz->time_limit) {
            return response()->json(['unlimited' => true]);
        }

        if (is_null($attempt->started_at)) {
            $attempt->started_at = now();
            $attempt->save();
        }

        $startedAt = $attempt->started_at->copy();
        $deadline = $startedAt->addMinutes($quiz->time_limit);
        $now = now();

        if ($now->gte($deadline)) {
            $this->autoSubmitAttempt($quiz, $attempt);
            return response()->json(['expired' => true]);
        }

        $remainingSeconds = (int) $now->diffInSeconds($deadline, false);

        return response()->json([
            'remaining_seconds' => max(0, $remainingSeconds),
            'deadline' => $deadline->toISOString(),
            'formatted_time' => $this->formatTime($remainingSeconds)
        ]);
    }

    private function formatTime(int $seconds): string
    {
        $minutes = intval($seconds / 60);
        $secs = $seconds % 60;
        return sprintf('%02d:%02d', $minutes, $secs);
    }

    private function autoSubmitAttempt(Quiz $quiz, QuizAttempt $attempt)
    {
        if ($attempt->completed_at) {
            return;
        }

        if (is_null($attempt->started_at)) {
            $attempt->started_at = now();
            $attempt->save();
        }

        $user = Auth::user();
        $savedAnswers = session()->get("quiz_progress_{$quiz->id}_{$user->id}", []);

        $score = 0;

        foreach ($savedAnswers as $answerData) {
            if (empty($answerData['question_id'])) continue;
            if (empty($answerData['option_id']) && empty($answerData['answer_text'])) continue;

            $question = Question::with('options')->find($answerData['question_id']);
            if (!$question) continue;

            $optionToSaveId = null;

            if ($question->type === 'multiple_choice' && !empty($answerData['option_id'])) {
                $selectedOption = $question->options->find($answerData['option_id']);
                if ($selectedOption) {
                    $optionToSaveId = $selectedOption->id;
                    if ($selectedOption->is_correct) {
                        $score += $question->marks;
                    }
                }
            } elseif ($question->type === 'true_false' && !empty($answerData['answer_text'])) {
                $selectedOption = $question->options->firstWhere('option_text', $answerData['answer_text']);
                if ($selectedOption) {
                    $optionToSaveId = $selectedOption->id;
                    if ($selectedOption->is_correct) {
                        $score += $question->marks;
                    }
                }
            }

            if ($optionToSaveId !== null) {
                $existingAnswer = QuestionAnswer::where([
                    'user_id' => $user->id,
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ])->first();

                if (!$existingAnswer) {
                    QuestionAnswer::create([
                        'user_id' => $user->id,
                        'quiz_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'option_id' => $optionToSaveId,
                    ]);
                }
            }
        }

        // ✅ FIX: Hitung pass_marks dari passing_percentage
        // Kolom pass_marks sudah dihapus, gunakan passing_percentage
        // ⚠️ CRITICAL FIX: Load questions jika belum ter-load untuk menghindari totalMarks = 0
        if (!$quiz->relationLoaded('questions')) {
            $quiz->load('questions');
        }

        $totalMarks = $quiz->questions->sum('marks');
        $passingMarks = ($totalMarks * ($quiz->passing_percentage ?? 70)) / 100;

        $attempt->update([
            'score' => $score,
            'passed' => ($score >= $passingMarks),
            'completed_at' => now(),
        ]);

        session()->forget("quiz_progress_{$quiz->id}_{$user->id}");
    }

    /**
     * Submit a quiz attempt.
     */
    public function submitAttempt(Request $request, Quiz $quiz, QuizAttempt $attempt)
    {
        $user = Auth::user();

        if ($attempt->user_id !== $user->id || $attempt->quiz_id !== $quiz->id) {
            abort(403, 'Akses ditolak.');
        }

        if ($attempt->completed_at) {
            return redirect()->route('quizzes.result', ['quiz' => $quiz, 'attempt' => $attempt])
                ->with('info', 'Anda sudah menyelesaikan percobaan ini.');
        }

        if (is_null($attempt->started_at)) {
            $attempt->started_at = now();
            $attempt->save();
        }

        if ($quiz->time_limit) {
            $startedAt = $attempt->started_at->copy();
            $deadline = $startedAt->addMinutes($quiz->time_limit);
            if (now() > $deadline) {
                $this->autoSubmitAttempt($quiz, $attempt);
                return redirect()->route('quizzes.result', ['quiz' => $quiz, 'attempt' => $attempt])
                    ->with('warning', 'Waktu kuis telah habis. Jawaban yang sudah terisi akan dinilai.');
            }
        }

        $validatedData = $request->validate([
            'answers' => 'present|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.option_id' => 'nullable|exists:options,id',
            'answers.*.answer_text' => 'nullable|string|in:True,False',
        ]);

        $score = 0;
        foreach ($validatedData['answers'] as $answerData) {
            if (empty($answerData['option_id']) && empty($answerData['answer_text'])) {
                continue;
            }

            $question = Question::with('options')->find($answerData['question_id']);
            if (!$question) continue;

            $isCorrect = false;
            $optionToSaveId = null;

            if ($question->type === 'multiple_choice') {
                if (!empty($answerData['option_id'])) {
                    $selectedOption = $question->options->find($answerData['option_id']);
                    if ($selectedOption) {
                        $optionToSaveId = $selectedOption->id;
                        if ($selectedOption->is_correct) {
                            $isCorrect = true;
                            $score += $question->marks;
                        }
                    }
                }
            } elseif ($question->type === 'true_false') {
                if (!empty($answerData['answer_text'])) {
                    $selectedOption = $question->options->firstWhere('option_text', $answerData['answer_text']);
                    if ($selectedOption) {
                        $optionToSaveId = $selectedOption->id;
                        if ($selectedOption->is_correct) {
                            $isCorrect = true;
                            $score += $question->marks;
                        }
                    }
                }
            }

            if ($optionToSaveId !== null) {
                QuestionAnswer::create([
                    'user_id' => $user->id,
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'option_id' => $optionToSaveId,
                ]);
            }
        }

        // ✅ FIX: Hitung pass_marks dari passing_percentage
        // ⚠️ CRITICAL FIX: Load questions jika belum ter-load untuk menghindari totalMarks = 0
        if (!$quiz->relationLoaded('questions')) {
            $quiz->load('questions');
        }

        $totalMarks = $quiz->questions->sum('marks');
        $passingMarks = ($totalMarks * ($quiz->passing_percentage ?? 70)) / 100;

        $attempt->score = $score;
        $attempt->passed = ($score >= $passingMarks);
        $attempt->completed_at = now();
        $attempt->save();

        session()->forget("quiz_progress_{$quiz->id}_{$user->id}");

        return redirect()->route('quizzes.result', [
            'quiz' => $quiz,
            'attempt' => $attempt
        ])->with('success', 'Kuis berhasil diselesaikan!');
    }

    /**
     * Show the quiz result.
     */
    public function showResult(Quiz $quiz, QuizAttempt $attempt)
    {
        $user = Auth::user();

        if ($attempt->user_id !== $user->id || $attempt->quiz_id !== $quiz->id) {
            abort(403, 'Akses ditolak.');
        }

        if (!$attempt->completed_at) {
            return redirect()->route('quizzes.start_attempt', $quiz)->with('error', 'Percobaan kuis belum selesai.');
        }
        
        $attempt->load('answers.question.options', 'user');

        // =================================================================
        // PERUBAHAN DIMULAI DI SINI
        // =================================================================

        // Ambil data content yang terkait dengan quiz ini
        $content = Content::where('quiz_id', $quiz->id)->firstOrFail();
        
        // Ambil skor dari attempt yang sudah disimpan
        $score = $attempt->score;
        // ✅ FIX: Hitung total_marks dari questions karena kolom total_marks sudah dihapus
        // ⚠️ CRITICAL FIX: Load questions jika belum ter-load
        if (!$quiz->relationLoaded('questions')) {
            $quiz->load('questions');
        }

        $total_marks = $quiz->questions->sum('marks');
        $score_percentage = ($total_marks > 0) ? ($score / $total_marks) * 100 : 0;
        
        // Cek apakah attempt saat ini lulus
        $isPassed = $attempt->passed;

        // Cek apakah pengguna PERNAH lulus kuis ini sebelumnya dari semua attempt
        $hasPassedQuizBefore = $user->quizAttempts()
                                    ->where('quiz_id', $quiz->id)
                                    ->where('passed', true) // Cek kolom 'passed' yang sudah ada
                                    ->exists();

        // Kirim semua data yang dibutuhkan ke view
        return view('quizzes.result', compact(
            'quiz',
            'attempt',
            'content',
            'score',
            'total_marks',
            'score_percentage',
            'isPassed',
            'hasPassedQuizBefore' // Variabel baru untuk view
        ));
        
        // =================================================================
        // PERUBAHAN SELESAI DI SINI
        // =================================================================
    }

    /**
     * Get quiz question form partial for AJAX.
     */
    public function getQuestionFormPartial(Request $request)
    {
        $question_loop_index = $request->query('index');
        return view('quizzes.partials.question-form-fields', compact('question_loop_index'));
    }

    /**
     * Get full quiz form partial for AJAX.
     */
    public function getFullQuizFormPartial()
    {
        return view('quizzes.partials.full-quiz-form');
    }

    public function start(Quiz $quiz)
    {
        // ✅ FIX: Eager load lesson and course untuk authorization
        // Ini penting untuk quiz yang baru diduplikasi agar lesson relationship fresh dari database
        $quiz->load('lesson.course');

        $user = Auth::user();

        // Bypass authorization for managers
        if (!$user->can('manage all courses')) {
            $this->authorize('start', $quiz);
        }

        return view('quizzes.start', compact('quiz'));
    }

    /**
     * Show leaderboard for quiz
     */
    public function leaderboard(Quiz $quiz)
    {
        // Check if leaderboard is enabled
        if (!$quiz->enable_leaderboard) {
            abort(404, 'Leaderboard tidak tersedia untuk quiz ini.');
        }

        // Get leaderboard data (best attempts per user)
        $leaderboard = $quiz->getLeaderboardWithBestAttempts();

        return view('quizzes.leaderboard', compact('quiz', 'leaderboard'));
    }

    public function attempt(Quiz $quiz)
    {
        $user = Auth::user();

        // Bypass authorization for managers
        if (!$user->can('manage all courses')) {
            $this->authorize('start', $quiz);
        }

        // Managers bypass check
        if (!$user->can('manage all courses')) {
            // =================================================================
            // PENAMBAHAN LOGIKA YANG SAMA DI SINI
            // =================================================================
            $hasPassedQuizBefore = $user->quizAttempts()
                                        ->where('quiz_id', $quiz->id)
                                        ->where('passed', true)
                                        ->exists();

            if ($hasPassedQuizBefore) {
                $lastPassedAttempt = $user->quizAttempts()
                                          ->where('quiz_id', $quiz->id)
                                          ->where('passed', true)
                                          ->latest('completed_at')
                                          ->first();

                return redirect()->route('quizzes.result', ['quiz' => $quiz, 'attempt' => $lastPassedAttempt])
                       ->with('info', 'Anda sudah lulus kuis ini dan tidak dapat mengerjakannya kembali.');
            }
            // =================================================================
            // AKHIR PENAMBAHAN
            // =================================================================
        }

        if ($quiz->status !== 'published') {
            return redirect()->back()->with('error', 'Kuis ini belum dipublikasikan.');
        }

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'started_at' => now(),
        ]);

        return view('quizzes.attempt', compact('quiz', 'attempt'));
    }

    /**
     * Show import form
     */
    public function showImport()
    {
        $this->authorize('create', Quiz::class);

        $lessons = Lesson::whereHas('course', function ($query) {
            $query->where('user_id', Auth::id())
                  ->orWhereHas('instructors', function ($q) {
                      $q->where('user_id', Auth::id());
                  });
        })->get();

        return view('quizzes.import', compact('lessons'));
    }

    /**
     * Import quizzes from Excel
     */
    public function import(Request $request)
    {
        $this->authorize('create', Quiz::class);

        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $import = new \App\Imports\QuizImport($request->lesson_id, Auth::id());
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));

            $errors = $import->getErrors();
            $successCount = $import->getSuccessCount();

            if ($successCount > 0) {
                $message = "{$successCount} quiz berhasil diimport.";

                if (count($errors) > 0) {
                    $message .= " Namun terdapat " . count($errors) . " error.";
                    session()->flash('import_errors', $errors);
                }

                return redirect()->route('quizzes.index')
                    ->with('success', $message);
            } else {
                return redirect()->back()
                    ->with('error', 'Tidak ada quiz yang berhasil diimport.')
                    ->with('import_errors', $errors);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template
     */
    public function downloadTemplate()
    {
        // Skip authorization for template download - anyone can download template
        // $this->authorize('create', Quiz::class);

        $headers = [
            'quiz_title',
            'quiz_description',
            'passing_percentage',
            'time_limit',
            'show_answers_after_attempt',
            'enable_leaderboard',
            'status',
            'question_text',
            'question_type',
            'marks',
            'option_1',
            'option_2',
            'option_3',
            'option_4',
            'option_5',
            'option_6',
            'option_7',
            'option_8',
            'option_9',
            'option_10',
            'correct_answer',
        ];

        $sampleData = [
            [
                'quiz_title' => 'Sample Quiz - Matematika Dasar',
                'quiz_description' => 'Quiz ini berisi pertanyaan matematika dasar',
                'passing_percentage' => 70,
                'time_limit' => 30,
                'show_answers_after_attempt' => 'yes',
                'enable_leaderboard' => 'yes',
                'status' => 'draft',
                'question_text' => 'Berapa hasil dari 2 + 2?',
                'question_type' => 'multiple_choice',
                'marks' => 10,
                'option_1' => '3',
                'option_2' => '4',
                'option_3' => '5',
                'option_4' => '6',
                'option_5' => '',
                'option_6' => '',
                'option_7' => '',
                'option_8' => '',
                'option_9' => '',
                'option_10' => '',
                'correct_answer' => '4',
            ],
            [
                'quiz_title' => 'Sample Quiz - Matematika Dasar',
                'quiz_description' => 'Quiz ini berisi pertanyaan matematika dasar',
                'passing_percentage' => 70,
                'time_limit' => 30,
                'show_answers_after_attempt' => 'yes',
                'enable_leaderboard' => 'yes',
                'status' => 'draft',
                'question_text' => 'Apakah 5 adalah bilangan prima?',
                'question_type' => 'true_false',
                'marks' => 10,
                'option_1' => '',
                'option_2' => '',
                'option_3' => '',
                'option_4' => '',
                'option_5' => '',
                'option_6' => '',
                'option_7' => '',
                'option_8' => '',
                'option_9' => '',
                'option_10' => '',
                'correct_answer' => 'true',
            ],
            [
                'quiz_title' => 'Sample Quiz - Matematika Dasar',
                'quiz_description' => 'Quiz ini berisi pertanyaan matematika dasar',
                'passing_percentage' => 70,
                'time_limit' => 30,
                'show_answers_after_attempt' => 'yes',
                'enable_leaderboard' => 'yes',
                'status' => 'draft',
                'question_text' => 'Berapa hasil dari 10 x 5?',
                'question_type' => 'multiple_choice',
                'marks' => 10,
                'option_1' => '45',
                'option_2' => '50',
                'option_3' => '55',
                'option_4' => '60',
                'option_5' => '',
                'option_6' => '',
                'option_7' => '',
                'option_8' => '',
                'option_9' => '',
                'option_10' => '',
                'correct_answer' => '50',
            ],
        ];

        try {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\QuizTemplateExport($headers, $sampleData),
                'quiz_import_template.xlsx'
            );
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }
}
