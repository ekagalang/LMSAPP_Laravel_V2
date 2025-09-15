<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reflection extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'mood',
        'tags',
        'visibility',
        'requires_response',
        'instructor_response',
        'responded_by',
        'responded_at'
    ];

    protected $casts = [
        'tags' => 'array',
        'requires_response' => 'boolean',
        'responded_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function getMoodEmojiAttribute(): string
    {
        return match($this->mood) {
            'very_sad' => '😢',
            'sad' => '😔',
            'neutral' => '😐',
            'happy' => '😊',
            'very_happy' => '😄',
            default => '😐'
        };
    }

    public function getMoodLabelAttribute(): string
    {
        return match($this->mood) {
            'very_sad' => 'Very Sad',
            'sad' => 'Sad',
            'neutral' => 'Neutral',
            'happy' => 'Happy',
            'very_happy' => 'Very Happy',
            default => 'Neutral'
        };
    }

    public function getVisibilityLabelAttribute(): string
    {
        return match($this->visibility) {
            'private' => 'Private (Only You)',
            'instructors_only' => 'Instructors Only',
            'public' => 'Public',
            default => 'Instructors Only'
        };
    }

    public function hasResponse(): bool
    {
        return !empty($this->instructor_response);
    }

    public function needsResponse(): bool
    {
        return $this->requires_response && !$this->hasResponse();
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRequiringResponse($query)
    {
        return $query->where('requires_response', true)
                    ->whereNull('instructor_response');
    }

    public function scopeVisibleToInstructors($query)
    {
        return $query->whereIn('visibility', ['instructors_only', 'public']);
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    /**
     * Optimized scope for recent reflections with minimal data
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->select(['id', 'user_id', 'title', 'mood', 'requires_response', 'instructor_response', 'created_at'])
                    ->orderBy('created_at', 'desc')
                    ->limit($limit);
    }

    /**
     * Optimized scope for analytics data
     */
    public function scopeForAnalytics($query)
    {
        return $query->select(['id', 'mood', 'requires_response', 'instructor_response', 'created_at']);
    }
}
