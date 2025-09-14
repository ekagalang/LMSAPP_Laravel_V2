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
        'sort_order'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean'
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
