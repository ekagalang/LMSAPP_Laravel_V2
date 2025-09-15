<?php

namespace App\Http\Controllers;

use App\Models\AudioLesson;
use App\Models\AudioExercise;
use App\Models\UserAudioProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AudioLearningController extends Controller
{
    public function index(Request $request)
    {
        $difficulty = $request->get('difficulty', 'all');

        $query = AudioLesson::active()->orderBy('sort_order');

        if ($difficulty !== 'all') {
            $query->byDifficulty($difficulty);
        }

        $lessons = $query->with(['exercises' => function($q) {
            $q->active()->orderBy('sort_order');
        }])->get();

        // Get user progress for all lessons
        $userProgress = [];
        if (Auth::check()) {
            $progressRecords = UserAudioProgress::where('user_id', Auth::id())
                ->whereIn('audio_lesson_id', $lessons->pluck('id'))
                ->get()
                ->groupBy('audio_lesson_id');

            foreach ($progressRecords as $lessonId => $progresses) {
                $userProgress[$lessonId] = [
                    'completed' => $progresses->where('completed', true)->count(),
                    'total' => $progresses->count(),
                    'score' => $progresses->sum('score'),
                    'max_score' => $progresses->sum('max_score')
                ];
            }
        }

        return view('audio-learning.index', compact('lessons', 'userProgress', 'difficulty'));
    }

    public function lesson($id)
    {
        $lesson = AudioLesson::active()
            ->with(['exercises' => function($q) {
                $q->active()->orderBy('sort_order');
            }])
            ->findOrFail($id);

        $userProgress = null;
        if (Auth::check()) {
            $userProgress = UserAudioProgress::firstOrCreate([
                'user_id' => Auth::id(),
                'audio_lesson_id' => $lesson->id,
                'audio_exercise_id' => null
            ], [
                'started_at' => now(),
                'max_score' => $lesson->exercises->sum('points')
            ]);
        }

        return view('audio-learning.lesson', compact('lesson', 'userProgress'));
    }

    public function exercise($lessonId, $exerciseId)
    {
        $lesson = AudioLesson::active()->findOrFail($lessonId);
        $exercise = AudioExercise::active()
            ->where('audio_lesson_id', $lessonId)
            ->findOrFail($exerciseId);

        $userProgress = null;
        if (Auth::check()) {
            $userProgress = UserAudioProgress::firstOrCreate([
                'user_id' => Auth::id(),
                'audio_lesson_id' => $lesson->id,
                'audio_exercise_id' => $exercise->id
            ], [
                'started_at' => now(),
                'max_score' => $exercise->points
            ]);
        }

        return view('audio-learning.exercise', compact('lesson', 'exercise', 'userProgress'));
    }

    public function updateProgress(Request $request, $lessonId): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'position' => 'required|integer|min:0',
            'exercise_id' => 'nullable|integer|exists:audio_exercises,id'
        ]);

        $progress = UserAudioProgress::where([
            'user_id' => Auth::id(),
            'audio_lesson_id' => $lessonId,
            'audio_exercise_id' => $validated['exercise_id'] ?? null
        ])->first();

        if ($progress) {
            $progress->updatePosition($validated['position']);
        }

        return response()->json(['success' => true]);
    }

    public function submitAnswer(Request $request, $lessonId, $exerciseId): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'answer' => 'required',
            'type' => 'required|in:text,speech,multiple_choice'
        ]);

        $exercise = AudioExercise::findOrFail($exerciseId);
        $isCorrect = $exercise->checkAnswer($validated['answer']);
        $points = $isCorrect ? $exercise->points : 0;

        $progress = UserAudioProgress::where([
            'user_id' => Auth::id(),
            'audio_lesson_id' => $lessonId,
            'audio_exercise_id' => $exerciseId
        ])->first();

        if ($progress) {
            $progress->recordAnswer($exerciseId, $validated['answer'], $isCorrect, $points);

            if ($isCorrect) {
                $progress->markCompleted($points);
            }
        }

        return response()->json([
            'success' => true,
            'correct' => $isCorrect,
            'points' => $points,
            'correct_answer' => $isCorrect ? null : $exercise->correct_answers[0] ?? null,
            'explanation' => $exercise->explanation ?? null
        ]);
    }

    public function submitSpeech(Request $request, $lessonId, $exerciseId): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'transcript' => 'required|string',
            'confidence' => 'nullable|numeric|min:0|max:1'
        ]);

        $exercise = AudioExercise::findOrFail($exerciseId);
        $progress = UserAudioProgress::where([
            'user_id' => Auth::id(),
            'audio_lesson_id' => $lessonId,
            'audio_exercise_id' => $exerciseId
        ])->first();

        if ($progress) {
            $progress->recordSpeechAttempt(
                $exerciseId,
                $validated['transcript'],
                $validated['confidence'] ?? 0
            );
        }

        $isCorrect = $exercise->checkAnswer($validated['transcript']);
        $points = $isCorrect ? $exercise->points : 0;

        if ($isCorrect && $progress) {
            $progress->recordAnswer($exerciseId, $validated['transcript'], true, $points);
            $progress->markCompleted($points);
        }

        return response()->json([
            'success' => true,
            'correct' => $isCorrect,
            'points' => $points,
            'confidence' => $validated['confidence'] ?? 0,
            'transcript' => $validated['transcript']
        ]);
    }

    public function getProgress($lessonId): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $progress = UserAudioProgress::where([
            'user_id' => Auth::id(),
            'audio_lesson_id' => $lessonId
        ])->get();

        $totalScore = $progress->sum('score');
        $maxScore = $progress->sum('max_score');
        $completed = $progress->where('completed', true)->count();
        $total = $progress->count();

        return response()->json([
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0,
            'completed_exercises' => $completed,
            'total_exercises' => $total,
            'progress_percentage' => $total > 0 ? round(($completed / $total) * 100, 2) : 0
        ]);
    }

    /**
     * Show the form for creating a new audio lesson (Admin only)
     */
    public function create()
    {
        // Check if user is admin/instructor
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Unauthorized access');
        }

        return view('audio-learning.create');
    }

    /**
     * Store a newly created audio lesson (Admin only)
     */
    public function store(Request $request)
    {
        // Check if user is admin/instructor
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'audio_file' => 'required|file|mimes:mp3,wav,m4a,aac|max:51200', // 50MB max
            'transcript' => 'nullable|string',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'metadata' => 'nullable|array',
            'available_for_courses' => 'boolean',

            // Exercise data
            'exercises' => 'nullable|array',
            'exercises.*.question_text' => 'required_with:exercises|string',
            'exercises.*.type' => 'required_with:exercises|in:multiple_choice,fill_blank,speech_response,comprehension',
            'exercises.*.correct_answers' => 'required_with:exercises|array',
            'exercises.*.points' => 'required_with:exercises|integer|min:1|max:100',
            'exercises.*.explanation' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Handle audio file upload
            $audioPath = null;
            if ($request->hasFile('audio_file')) {
                $audioFile = $request->file('audio_file');
                $fileName = time() . '_' . $audioFile->getClientOriginalName();
                $audioPath = $audioFile->storeAs('audio/lessons', $fileName, 'public');

                // Get audio duration if possible
                $duration = null;
                try {
                    $fullPath = storage_path('app/public/' . $audioPath);
                    if (function_exists('getid3_lib') || class_exists('getID3')) {
                        // If getID3 library is available
                        $getID3 = new \getID3;
                        $fileInfo = $getID3->analyze($fullPath);
                        $duration = $fileInfo['playtime_seconds'] ?? null;
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not get audio duration: ' . $e->getMessage());
                }
            }

            // Create audio lesson
            $audioLesson = AudioLesson::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'audio_file_path' => $audioPath,
                'duration_seconds' => $duration,
                'difficulty_level' => $validated['difficulty_level'],
                'transcript' => $validated['transcript'],
                'metadata' => $validated['metadata'] ?? [],
                'is_active' => true,
                'sort_order' => AudioLesson::max('sort_order') + 1,
                'available_for_courses' => $validated['available_for_courses'] ?? false,
            ]);

            // Create exercises if provided
            if (!empty($validated['exercises'])) {
                foreach ($validated['exercises'] as $index => $exerciseData) {
                    AudioExercise::create([
                        'audio_lesson_id' => $audioLesson->id,
                        'title' => 'Exercise ' . ($index + 1),
                        'question' => $exerciseData['question_text'], // Map to correct field name
                        'exercise_type' => $exerciseData['type'], // Map to correct field name
                        'correct_answers' => $exerciseData['correct_answers'],
                        'points' => $exerciseData['points'],
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]);
                }
            }

            DB::commit();

            Log::info('Audio Learning Created', [
                'audio_lesson_id' => $audioLesson->id,
                'title' => $audioLesson->title,
                'exercises_count' => count($validated['exercises'] ?? []),
                'created_by' => Auth::id()
            ]);

            return redirect()->route('audio-learning.index')
                ->with('success', 'Audio learning berhasil dibuat! 🎉');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Audio Learning Creation Failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withInput()
                ->with('error', 'Gagal membuat audio learning: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing an audio lesson (Admin only)
     */
    public function edit($id)
    {
        // Check if user is admin/instructor
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Unauthorized access');
        }

        $audioLesson = AudioLesson::with('exercises')->findOrFail($id);
        return view('audio-learning.edit', compact('audioLesson'));
    }

    /**
     * Update an audio lesson (Admin only)
     */
    public function update(Request $request, $id)
    {
        // Check if user is admin/instructor
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Unauthorized access');
        }

        $audioLesson = AudioLesson::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'audio_file' => 'nullable|file|mimes:mp3,wav,m4a,aac|max:51200',
            'transcript' => 'nullable|string',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'metadata' => 'nullable|array',
            'available_for_courses' => 'boolean',
        ]);

        // Handle audio file upload
        if ($request->hasFile('audio_file')) {
            // Delete old file
            if ($audioLesson->audio_file_path) {
                Storage::disk('public')->delete($audioLesson->audio_file_path);
            }

            $audioFile = $request->file('audio_file');
            $fileName = time() . '_' . $audioFile->getClientOriginalName();
            $validated['audio_file_path'] = $audioFile->storeAs('audio/lessons', $fileName, 'public');
        }

        $audioLesson->update($validated);

        return redirect()->route('audio-learning.index')
            ->with('success', 'Audio learning berhasil diperbarui! ✅');
    }

    /**
     * Delete an audio lesson (Admin only)
     */
    public function destroy($id)
    {
        // Check if user is admin/instructor
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Unauthorized access');
        }

        $audioLesson = AudioLesson::findOrFail($id);

        // Delete audio file
        if ($audioLesson->audio_file_path) {
            Storage::disk('public')->delete($audioLesson->audio_file_path);
        }

        $audioLesson->delete();

        return redirect()->route('audio-learning.index')
            ->with('success', 'Audio learning berhasil dihapus! 🗑️');
    }
}
