<?php
// app/Models/CoursePeriod.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CoursePeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'start_date',
        'end_date',
        'status',
        'description',
        'max_participants',
        'join_token'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Auto-update status when saving
    protected static function booted()
    {
        static::creating(function ($period) {
            if (empty($period->join_token)) {
                $period->generateJoinToken();
            }
        });

        static::saving(function ($period) {
            $period->updateStatusBasedOnDates();
        });
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function instructors()
    {
        return $this->belongsToMany(User::class, 'course_period_instructor');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'course_period_user')->withPivot('feedback')->withTimestamps();
    }

    public function enrolledUsers()
    {
        return $this->participants();
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')
            ->where('start_date', '>', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')
            ->where('end_date', '<', now());
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('course', function ($q) use ($userId) {
            $q->where(function ($sq) use ($userId) {
                $sq->whereHas('enrolledUsers', function ($eq) use ($userId) {
                    $eq->where('user_id', $userId);
                })->orWhereHas('instructors', function ($iq) use ($userId) {
                    $iq->where('user_id', $userId);
                })->orWhereHas('eventOrganizers', function ($oq) use ($userId) {
                    $oq->where('user_id', $userId);
                });
            });
        });
    }

    // ========================================
    // METHODS
    // ========================================

    public function isActive(): bool
    {
        return $this->status === 'active' &&
            $this->start_date <= now() &&
            $this->end_date >= now();
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming' && $this->start_date > now();
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed' || $this->end_date < now();
    }

    public function isChatAllowed(): bool
    {
        return $this->isActive();
    }

    public function updateStatusBasedOnDates(): void
    {
        $now = now();

        if ($now < $this->start_date) {
            $this->status = 'upcoming';
        } elseif ($now >= $this->start_date && $now <= $this->end_date) {
            $this->status = 'active';
        } else {
            $this->status = 'completed';
        }
    }

    public function hasUser($userId): bool
    {
        return $this->participants()->where('users.id', $userId)->exists() ||
               $this->instructors()->where('users.id', $userId)->exists() ||
               $this->course->eventOrganizers()->where('users.id', $userId)->exists();
    }

    public function isInstructor($userId): bool
    {
        return $this->instructors()->where('users.id', $userId)->exists();
    }

    public function isParticipant($userId): bool
    {
        return $this->participants()->where('users.id', $userId)->exists();
    }

    public function getParticipantCount(): int
    {
        return $this->participants()->count();
    }

    public function hasAvailableSlots(): bool
    {
        if (!$this->max_participants) {
            return true;
        }
        
        return $this->getParticipantCount() < $this->max_participants;
    }

    public function getAvailableSlots(): int
    {
        if (!$this->max_participants) {
            return PHP_INT_MAX;
        }
        
        return max(0, $this->max_participants - $this->getParticipantCount());
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'active' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>',
            'upcoming' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Mendatang</span>',
            'completed' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Selesai</span>',
        ];

        return $badges[$this->status] ?? $badges['upcoming'];
    }

    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getRemainingDays(): int
    {
        if ($this->isCompleted()) {
            return 0;
        }

        return now()->diffInDays($this->end_date, false);
    }

    // ========================================
    // TOKEN METHODS
    // ========================================

    public function generateJoinToken(): string
    {
        do {
            $token = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8));
        } while (self::where('join_token', $token)->exists());

        $this->join_token = $token;
        return $token;
    }

    public function regenerateJoinToken(): string
    {
        $this->generateJoinToken();
        $this->save();
        return $this->join_token;
    }

    public static function findByToken(string $token): ?self
    {
        return self::where('join_token', strtoupper($token))->first();
    }

    public function canJoinWithToken($userId = null): bool
    {
        // Check if period is still accepting participants
        if ($this->isCompleted()) {
            return false;
        }

        // Check if there are available slots
        if (!$this->hasAvailableSlots()) {
            return false;
        }

        // If user is provided, check if they are already enrolled
        if ($userId && $this->isParticipant($userId)) {
            return false;
        }

        return true;
    }
}
