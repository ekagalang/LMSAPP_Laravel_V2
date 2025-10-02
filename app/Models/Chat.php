<?php
// app/Models/Chat.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_class_id',
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

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class);
    }

    // Alias for backward compatibility
    public function coursePeriod()
    {
        return $this->courseClass();
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
        return $this->participants(); // For now, all participants are active

        // You can add conditions like:
        // return $this->participants()->where('status', 'active');
        // or check last seen timestamp, etc.
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

    public function scopeForUser($query, $userId = null)
    {
        $userId = $userId ?: auth()->id();

        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopeWithActiveClass($query)
    {
        return $query->whereHas('courseClass', function ($q) {
            $q->active();
        });
    }

    // Alias for backward compatibility
    public function scopeWithActivePeriod($query)
    {
        return $this->scopeWithActiveClass($query);
    }

    // ========================================
    // METHODS
    // ========================================

    public function hasParticipant($userId)
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

    public function updateLastMessage()
    {
        $this->touch(); // Updates updated_at timestamp
    }

    public function getDisplayName()
    {
        if ($this->name) {
            return $this->name;
        }

        if ($this->type === 'direct') {
            // For direct chats, show other participant's name
            $otherParticipant = $this->participants()
                ->where('user_id', '!=', auth()->id())
                ->first();

            return $otherParticipant ? $otherParticipant->name : 'Unknown User';
        }

        // For group chats without name, create one from participants
        $participantNames = $this->participants()
            ->where('user_id', '!=', auth()->id())
            ->pluck('name')
            ->take(3)
            ->implode(', ');

        if ($this->participants()->count() > 4) {
            $participantNames .= ' and ' . ($this->participants()->count() - 4) . ' others';
        }

        return $participantNames ?: 'Group Chat';
    }

    public function isChatAllowed(): bool
    {
        if (!$this->courseClass) {
            return true; // Global admin chat
        }

        return $this->courseClass->isChatAllowed();
    }

    public function getContextInfo(): array
    {
        if (!$this->courseClass) {
            return [
                'type' => 'global',
                'context' => 'Admin Chat'
            ];
        }

        return [
            'type' => 'course',
            'context' => $this->courseClass->course->title,
            'class' => $this->courseClass->name,
            'status' => $this->courseClass->status
        ];
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function getUnreadCountForUser($userId = null)
    {
        $userId = $userId ?: auth()->id();

        // Get the last read timestamp for this user in this chat
        $participant = $this->participants()
            ->where('user_id', $userId)
            ->first();

        if (!$participant || !$participant->pivot->last_read_at) {
            // If no read timestamp, all messages are unread
            return $this->messages()->count();
        }

        return $this->messages()
            ->where('created_at', '>', $participant->pivot->last_read_at)
            ->where('user_id', '!=', $userId) // Don't count own messages
            ->count();
    }

    public function markAsReadForUser($userId = null)
    {
        $userId = $userId ?: auth()->id();

        $this->participants()
            ->updateExistingPivot($userId, [
                'last_read_at' => now()
            ]);
    }

    public function otherParticipants($userId = null)
    {
        $userId = $userId ?: auth()->id();

        return $this->participants()->where('user_id', '!=', $userId);
    }

    protected static function boot()
    {
        parent::boot();
        /*
        // When a chat is created, add creator as participant if not already added
        static::created(function ($chat) {
            if (!$chat->hasParticipant($chat->created_by)) {
                $chat->participants()->attach($chat->created_by, [
                    'joined_at' => now()
                ]);
            }
        });
        */
    }
}
