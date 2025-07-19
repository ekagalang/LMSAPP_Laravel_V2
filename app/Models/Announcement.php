<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
    public function user()
    {
        return $this->belongsTo(User::class);
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
