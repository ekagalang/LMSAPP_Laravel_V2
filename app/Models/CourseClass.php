<?php
// app/Models/CourseClass.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\TokenGenerator;

class CourseClass extends Model
{
    use HasFactory;

    protected $table = 'course_classes';

    protected $fillable = [
        'course_id',
        'name',
        'class_code',
        'start_date',
        'end_date',
        'status',
        'description',
        'max_participants',
        'enrollment_token',
        'token_enabled',
        'token_expires_at',
        'token_type'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'token_enabled' => 'boolean',
        'token_expires_at' => 'datetime',
    ];

    // Auto-update status when saving
    protected static function booted()
    {
        static::saving(function ($class) {
            // Auto-update status based on dates if dates exist
            if ($class->start_date && $class->end_date) {
                $class->updateStatusBasedOnDates();
            }
        });

        static::creating(function ($class) {
            // Auto-generate class_code if not provided
            if (empty($class->class_code)) {
                $class->class_code = strtoupper(Str::random(8));
            }
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
        return $this->belongsToMany(User::class, 'course_class_instructor');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'course_class_user')->withPivot('feedback')->withTimestamps();
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
        // If no dates set, rely on status only
        if (!$this->start_date || !$this->end_date) {
            return $this->status === 'active';
        }

        return $this->status === 'active' &&
            $this->start_date <= now() &&
            $this->end_date >= now();
    }

    public function isUpcoming(): bool
    {
        // If no start date set, rely on status only
        if (!$this->start_date) {
            return $this->status === 'upcoming';
        }

        return $this->status === 'upcoming' && $this->start_date > now();
    }

    public function isCompleted(): bool
    {
        // If no end date set, rely on status only
        if (!$this->end_date) {
            return $this->status === 'completed';
        }

        return $this->status === 'completed' || $this->end_date < now();
    }

    public function isChatAllowed(): bool
    {
        return $this->isActive();
    }

    public function updateStatusBasedOnDates(): void
    {
        // Don't auto-update status if dates are not set
        if (!$this->start_date || !$this->end_date) {
            return;
        }

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
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        return $this->start_date->diffInDays($this->end_date);
    }

    public function getRemainingDays(): int
    {
        if ($this->isCompleted()) {
            return 0;
        }

        if (!$this->end_date) {
            return 0;
        }

        return now()->diffInDays($this->end_date, false);
    }

    // ========================================
    // TOKEN METHODS
    // ========================================

    /**
     * Generate enrollment token (random or custom)
     *
     * @param string $type 'random' or 'custom'
     * @param string|null $customToken Token kustom jika type = 'custom'
     * @param int $length Panjang token jika type = 'random'
     * @param string $format Format token: 'alphanumeric', 'numeric', 'alpha'
     * @return array ['success' => bool, 'token' => string, 'message' => string]
     */
    public function generateEnrollmentToken(
        string $type = 'random',
        ?string $customToken = null,
        int $length = 8,
        string $format = 'alphanumeric'
    ): array {
        try {
            if ($type === 'custom') {
                if (empty($customToken)) {
                    return [
                        'success' => false,
                        'token' => '',
                        'message' => 'Custom token tidak boleh kosong'
                    ];
                }

                $validation = TokenGenerator::validateUniqueCustomToken(
                    static::class,
                    $customToken,
                    'enrollment_token',
                    $this->id
                );

                if (!$validation['valid']) {
                    return [
                        'success' => false,
                        'token' => $validation['token'],
                        'message' => $validation['message']
                    ];
                }

                $token = $validation['token'];
            } else {
                // Generate random token
                $token = TokenGenerator::generateUniqueRandom(
                    static::class,
                    $length,
                    $format,
                    'enrollment_token'
                );
            }

            $this->enrollment_token = $token;
            $this->token_type = $type;
            $this->token_enabled = true;
            $this->save();

            return [
                'success' => true,
                'token' => $token,
                'message' => 'Token berhasil dibuat'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'token' => '',
                'message' => 'Gagal membuat token: ' . $e->getMessage()
            ];
        }
    }

    public function disableToken(): void
    {
        $this->token_enabled = false;
        $this->save();
    }

    public function isTokenValid(): bool
    {
        if (!$this->token_enabled || !$this->enrollment_token) {
            return false;
        }

        if ($this->token_expires_at && $this->token_expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function validateToken(string $token): bool
    {
        return $this->enrollment_token === strtoupper($token) && $this->isTokenValid();
    }

    /**
     * Get token type label
     */
    public function getTokenTypeLabel(): string
    {
        return match($this->token_type) {
            'custom' => 'Custom',
            'random' => 'Random',
            default => 'Random',
        };
    }
}
