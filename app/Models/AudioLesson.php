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
        'video_file_path',
        'content_type',
        'duration_seconds',
        'difficulty_level',
        'transcript',
        'metadata',
        'video_metadata',
        'is_active',
        'sort_order',
        'available_for_courses'
    ];

    protected $casts = [
        'metadata' => 'array',
        'video_metadata' => 'array',
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
        return $this->audio_file_path ? asset('storage/' . $this->audio_file_path) : null;
    }

    public function getVideoUrlAttribute()
    {
        return $this->video_file_path ? asset('storage/' . $this->video_file_path) : null;
    }

    public function hasVideo(): bool
    {
        return !empty($this->video_file_path);
    }

    public function hasAudio(): bool
    {
        return !empty($this->audio_file_path);
    }

    public function isVideoType(): bool
    {
        return $this->content_type === 'video' || $this->content_type === 'mixed';
    }

    public function isAudioType(): bool
    {
        return $this->content_type === 'audio' || $this->content_type === 'mixed';
    }

    public function getMainMediaUrl(): string
    {
        if ($this->hasVideo()) {
            return $this->video_url;
        }
        return $this->audio_url;
    }

    public function getContentTypeIcon(): string
    {
        switch ($this->content_type) {
            case 'video':
                return '🎥';
            case 'mixed':
                return '🎬';
            default:
                return '🎵';
        }
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_seconds) return '00:00';

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
