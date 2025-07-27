<?php
// app/Models/Chat.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_period_id',
        'created_by',
        'name',
        'type',
        'is_active',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function coursePeriod()
    {
        return $this->belongsTo(CoursePeriod::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'chat_participants')
            ->withPivot(['joined_at', 'last_read_at', 'is_active'])
            ->withTimestamps();
    }

    public function activeParticipants()
    {
        return $this->participants()->wherePivot('is_active', true);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('is_active', true);
        });
    }

    public function scopeWithActivePeriod($query)
    {
        return $query->whereHas('coursePeriod', function ($q) {
            $q->active();
        });
    }

    // ========================================
    // METHODS
    // ========================================

    public function hasParticipant($userId): bool
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    public function addParticipant($userId): void
    {
        if (!$this->hasParticipant($userId)) {
            $this->participants()->attach($userId, [
                'joined_at' => now(),
                'is_active' => true
            ]);
        }
    }

    public function removeParticipant($userId): void
    {
        $this->participants()->updateExistingPivot($userId, [
            'is_active' => false
        ]);
    }

    public function updateLastMessage(): void
    {
        $this->update(['last_message_at' => now()]);
    }

    public function getDisplayName(): string
    {
        if ($this->type === 'group') {
            return $this->name ?? 'Group Chat';
        }

        // For direct chats, show the other participant's name
        $participants = $this->activeParticipants;
        if ($participants->count() === 2) {
            $currentUserId = auth()->id();
            $otherParticipant = $participants->where('id', '!=', $currentUserId)->first();
            return $otherParticipant ? $otherParticipant->name : 'Direct Chat';
        }

        return 'Chat';
    }

    public function isChatAllowed(): bool
    {
        if (!$this->coursePeriod) {
            return true; // Global admin chat
        }

        return $this->coursePeriod->isChatAllowed();
    }

    public function getContextInfo(): array
    {
        if (!$this->coursePeriod) {
            return [
                'type' => 'global',
                'context' => 'Admin Chat'
            ];
        }

        return [
            'type' => 'course',
            'context' => $this->coursePeriod->course->title,
            'period' => $this->coursePeriod->name,
            'status' => $this->coursePeriod->status
        ];
    }
}
