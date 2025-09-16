<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Assignment extends Model
{
    protected $fillable = [
        'title',
        'description',
        'instructions',
        'submission_type',
        'allowed_file_types',
        'max_file_size',
        'max_files',
        'due_date',
        'allow_late_submission',
        'late_submission_until',
        'late_penalty',
        'max_points',
        'is_active',
        'show_to_students',
        'created_by',
        'metadata'
    ];

    protected $casts = [
        'allowed_file_types' => 'array',
        'metadata' => 'array',
        'due_date' => 'datetime',
        'late_submission_until' => 'datetime',
        'is_active' => 'boolean',
        'show_to_students' => 'boolean',
        'allow_late_submission' => 'boolean',
        'late_penalty' => 'decimal:2'
    ];

    /**
     * Relationships
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('show_to_students', true);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('due_date', '>', now());
    }

    /**
     * Helper methods
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }

    public function acceptsLateSubmissions(): bool
    {
        if (!$this->allow_late_submission) {
            return false;
        }

        if ($this->late_submission_until) {
            return $this->late_submission_until->isFuture();
        }

        return true;
    }

    public function canSubmit(): bool
    {
        if (!$this->is_active || !$this->show_to_students) {
            return false;
        }

        if (!$this->isOverdue()) {
            return true;
        }

        return $this->acceptsLateSubmissions();
    }

    public function getSubmissionForUser($userId): ?AssignmentSubmission
    {
        return $this->submissions()->where('user_id', $userId)->latest()->first();
    }

    public function hasSubmissionFromUser($userId): bool
    {
        return $this->submissions()->where('user_id', $userId)->exists();
    }

    public function getFormattedFileSizeLimit(): string
    {
        if (!$this->max_file_size) {
            return 'No limit';
        }

        $bytes = $this->max_file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        return number_format($bytes / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

    public function getFileSizeFormatted(): string
    {
        return $this->getFormattedFileSizeLimit();
    }

    public function getAllowedFileTypesString(): string
    {
        if (!$this->allowed_file_types || empty($this->allowed_file_types)) {
            return 'All file types';
        }

        return implode(', ', $this->allowed_file_types);
    }

    public function getStatusBadgeClass(): string
    {
        if (!$this->is_active) {
            return 'bg-gray-100 text-gray-800';
        }

        if ($this->isOverdue()) {
            return $this->acceptsLateSubmissions()
                ? 'bg-yellow-100 text-yellow-800'
                : 'bg-red-100 text-red-800';
        }

        return 'bg-green-100 text-green-800';
    }

    public function getStatusText(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        if ($this->isOverdue()) {
            return $this->acceptsLateSubmissions() ? 'Late submissions allowed' : 'Closed';
        }

        return 'Open';
    }

    public function getSubmissionStats(): array
    {
        $total = $this->submissions()->count();
        $submitted = $this->submissions()->whereNotNull('submitted_at')->count();
        $graded = $this->submissions()->whereNotNull('grade')->count();
        $late = $this->submissions()->where('is_late', true)->count();

        return compact('total', 'submitted', 'graded', 'late');
    }
}
