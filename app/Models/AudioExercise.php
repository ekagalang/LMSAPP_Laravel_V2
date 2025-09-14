<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AudioExercise extends Model
{
    protected $fillable = [
        'audio_lesson_id',
        'title',
        'question',
        'exercise_type',
        'options',
        'correct_answers',
        'points',
        'audio_cue',
        'play_from_seconds',
        'play_to_seconds',
        'speech_recognition_keywords',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answers' => 'array',
        'speech_recognition_keywords' => 'array',
        'is_active' => 'boolean'
    ];

    public function audioLesson(): BelongsTo
    {
        return $this->belongsTo(AudioLesson::class);
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserAudioProgress::class);
    }

    public function userProgressFor($userId): ?UserAudioProgress
    {
        return $this->userProgress()->where('user_id', $userId)->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('exercise_type', $type);
    }

    public function checkAnswer($userAnswer): bool
    {
        $correctAnswers = $this->correct_answers;

        if (!is_array($correctAnswers)) {
            $correctAnswers = [$correctAnswers];
        }

        if (is_string($userAnswer)) {
            $userAnswer = strtolower(trim($userAnswer));
        }

        foreach ($correctAnswers as $correct) {
            if (is_string($correct)) {
                $correct = strtolower(trim($correct));
            }

            if ($userAnswer === $correct) {
                return true;
            }

            // For speech recognition, check similarity
            if ($this->exercise_type === 'speech_response') {
                $similarity = 0;
                similar_text($userAnswer, $correct, $similarity);
                if ($similarity >= 80) { // 80% similarity threshold
                    return true;
                }
            }
        }

        return false;
    }

    public function getAudioSegmentDuration(): int
    {
        if ($this->play_to_seconds && $this->play_from_seconds) {
            return $this->play_to_seconds - $this->play_from_seconds;
        }

        return $this->audioLesson->duration_seconds ?? 0;
    }
}
