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
        'max_participants'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Auto-update status when saving
    protected static function booted()
    {
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
        return $this->course->hasUser($userId);
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
}
