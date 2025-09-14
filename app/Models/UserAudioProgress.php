<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAudioProgress extends Model
{
    protected $fillable = [
        'user_id',
        'audio_lesson_id',
        'audio_exercise_id',
        'current_position_seconds',
        'completed',
        'score',
        'max_score',
        'answers',
        'speech_attempts',
        'attempts_count',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'answers' => 'array',
        'speech_attempts' => 'array',
        'completed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function audioLesson(): BelongsTo
    {
        return $this->belongsTo(AudioLesson::class);
    }

    public function audioExercise(): BelongsTo
    {
        return $this->belongsTo(AudioExercise::class);
    }

    public function markCompleted($score = null): void
    {
        $this->update([
            'completed' => true,
            'completed_at' => now(),
            'score' => $score ?? $this->score
        ]);
    }

    public function recordAnswer($exerciseId, $answer, $isCorrect, $points = 0): void
    {
        $answers = $this->answers ?? [];
        $answers[$exerciseId] = [
            'answer' => $answer,
            'is_correct' => $isCorrect,
            'points' => $points,
            'answered_at' => now()->toISOString()
        ];

        $this->update([
            'answers' => $answers,
            'score' => $this->score + ($isCorrect ? $points : 0),
            'attempts_count' => $this->attempts_count + 1
        ]);
    }

    public function recordSpeechAttempt($exerciseId, $transcript, $confidence = 0): void
    {
        $attempts = $this->speech_attempts ?? [];

        if (!isset($attempts[$exerciseId])) {
            $attempts[$exerciseId] = [];
        }

        $attempts[$exerciseId][] = [
            'transcript' => $transcript,
            'confidence' => $confidence,
            'attempted_at' => now()->toISOString()
        ];

        $this->update([
            'speech_attempts' => $attempts
        ]);
    }

    public function updatePosition($seconds): void
    {
        $this->update([
            'current_position_seconds' => $seconds
        ]);
    }

    public function getScorePercentageAttribute(): float
    {
        if ($this->max_score == 0) return 0;
        return round(($this->score / $this->max_score) * 100, 2);
    }

    public function getProgressPercentageAttribute(): float
    {
        if (!$this->audioLesson || !$this->audioLesson->duration_seconds) return 0;
        return round(($this->current_position_seconds / $this->audioLesson->duration_seconds) * 100, 2);
    }
}
