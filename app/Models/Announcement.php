<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'level',
        'target_roles',
        'published_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'target_roles' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Mendapatkan pengguna yang membuat pengumuman.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke reads - dengan error handling
     */
    public function reads(): HasMany
    {
        try {
            // Check if AnnouncementRead model exists and table exists
            if (
                class_exists('App\Models\AnnouncementRead') &&
                DB::getSchemaBuilder()->hasTable('announcement_reads')
            ) {
                return $this->hasMany(\App\Models\AnnouncementRead::class);
            }
        } catch (\Exception $e) {
            Log::warning('AnnouncementRead relationship not available: ' . $e->getMessage());
        }

        // Return empty relationship if AnnouncementRead doesn't exist
        return $this->hasMany(static::class, 'non_existent_column', 'non_existent_column')->whereRaw('1 = 0');
    }

    /**
     * Many-to-many relationship dengan users yang sudah membaca - dengan error handling
     */
    public function readByUsers(): BelongsToMany
    {
        try {
            if (
                class_exists('App\Models\AnnouncementRead') &&
                DB::getSchemaBuilder()->hasTable('announcement_reads')
            ) {
                return $this->belongsToMany(User::class, 'announcement_reads')
                    ->withPivot('read_at')
                    ->withTimestamps();
            }
        } catch (\Exception $e) {
            Log::warning('AnnouncementRead relationship not available: ' . $e->getMessage());
        }

        // Return empty relationship if table doesn't exist
        return $this->belongsToMany(User::class, 'non_existent_table')->whereRaw('1 = 0');
    }

    /**
     * Scope untuk pengumuman yang aktif dan sudah dipublikasi
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->where('published_at', '<=', now())
                    ->orWhereNull('published_at');
            })
            ->where(function ($q) {
                $q->where('expires_at', '>', now())
                    ->orWhereNull('expires_at');
            });
    }

    /**
     * Scope untuk pengumuman yang visible untuk user tertentu
     */
    public function scopeForUser(Builder $query, $user): Builder
    {
        $userRoles = $user->getRoleNames()->toArray();

        return $query->active()
            ->where(function ($q) use ($userRoles) {
                $q->whereNull('target_roles');
                foreach ($userRoles as $role) {
                    $q->orWhereJsonContains('target_roles', $role);
                }
            });
    }

    /**
     * Scope untuk pengumuman yang belum dibaca oleh user tertentu - dengan error handling
     */
    public function scopeUnreadForUser(Builder $query, $user): Builder
    {
        try {
            // Check if we can use the reads relationship
            if (
                class_exists('App\Models\AnnouncementRead') &&
                DB::getSchemaBuilder()->hasTable('announcement_reads')
            ) {
                return $query->forUser($user)
                    ->whereDoesntHave('reads', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            }
        } catch (\Exception $e) {
            Log::warning('Cannot use unread filtering: ' . $e->getMessage());
        }

        // If AnnouncementRead doesn't exist, return all announcements for user
        return $query->forUser($user);
    }

    /**
     * Mark announcement sebagai dibaca oleh user - dengan error handling
     */
    public function markAsReadBy(User $user)
    {
        try {
            if (
                class_exists('App\Models\AnnouncementRead') &&
                DB::getSchemaBuilder()->hasTable('announcement_reads')
            ) {
                return \App\Models\AnnouncementRead::firstOrCreate([
                    'announcement_id' => $this->id,
                    'user_id' => $user->id,
                ], [
                    'read_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Cannot mark announcement as read: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Cek apakah announcement sudah dibaca oleh user - dengan error handling
     */
    public function isReadByUser(User $user): bool
    {
        try {
            if (
                class_exists('App\Models\AnnouncementRead') &&
                DB::getSchemaBuilder()->hasTable('announcement_reads')
            ) {
                return $this->reads()
                    ->where('user_id', $user->id)
                    ->exists();
            }
        } catch (\Exception $e) {
            Log::warning('Cannot check read status: ' . $e->getMessage());
        }

        return false; // Default to unread if we can't check
    }

    /**
     * Get read count - dengan error handling
     */
    public function getReadCountAttribute(): int
    {
        try {
            if (
                class_exists('App\Models\AnnouncementRead') &&
                DB::getSchemaBuilder()->hasTable('announcement_reads')
            ) {
                return $this->reads()->count();
            }
        } catch (\Exception $e) {
            Log::warning('Cannot get read count: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * Get level color for display
     */
    public function getLevelColorAttribute(): string
    {
        return match ($this->level) {
            'success' => 'green',
            'warning' => 'yellow',
            'danger' => 'red',
            default => 'blue',
        };
    }

    /**
     * Get level icon for display
     */
    public function getLevelIconAttribute(): string
    {
        return match ($this->level) {
            'success' => 'check-circle',
            'warning' => 'exclamation-triangle',
            'danger' => 'x-circle',
            default => 'information-circle',
        };
    }

    /**
     * Check if announcement is currently published
     */
    public function getIsPublishedAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->published_at && $this->published_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if announcement is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get formatted target roles for display
     */
    public function getFormattedTargetRolesAttribute(): string
    {
        if (!$this->target_roles) {
            return 'Semua Pengguna';
        }

        $roleNames = [
            'participant' => 'Peserta',
            'instructor' => 'Instruktur',
            'event-organizer' => 'Event Organizer',
            'super-admin' => 'Super Admin'
        ];

        $formatted = collect($this->target_roles)
            ->map(fn($role) => $roleNames[$role] ?? $role)
            ->join(', ');

        return $formatted;
    }
}
