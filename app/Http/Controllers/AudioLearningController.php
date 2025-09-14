<?php

namespace App\Http\Controllers;

use App\Models\AudioLesson;
use App\Models\AudioExercise;
use App\Models\UserAudioProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
}
