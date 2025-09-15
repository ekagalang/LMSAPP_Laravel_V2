<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AudioLesson extends Model
{
    protected $fillable = [
        'title',
        'description',
        'audio_file_path',
        'duration_seconds',
        'difficulty_level',
        'transcript',
        'metadata',
        'is_active',
        'sort_order',
        'available_for_courses'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'available_for_courses' => 'boolean'
    ];

    public function exercises(): HasMany
    {
        return $this->hasMany(AudioExercise::class)->orderBy('sort_order');
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

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    public function scopeAvailableForCourses($query)
    {
        return $query->where('available_for_courses', true);
    }

    /**
     * Check if user has completed all required exercises for this audio lesson
     */
    public function hasUserCompletedExercises($userId): bool
    {
        if (!$userId) return false;

        $requiredExercises = $this->exercises()->where('is_active', true)->count();

        if ($requiredExercises === 0) {
            return true; // No exercises means auto-complete
        }

        $completedExercises = UserAudioProgress::where('user_id', $userId)
            ->where('audio_lesson_id', $this->id)
            ->where('completed', true)
            ->count();

        return $completedExercises >= $requiredExercises;
    }

    public function getAudioUrlAttribute()
    {
        return asset('storage/' . $this->audio_file_path);
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_seconds) return '00:00';

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
