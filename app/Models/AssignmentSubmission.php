<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id',
        'user_id',
        'submission_text',
        'submission_link',
        'file_paths',
        'file_metadata',
        'status',
        'submitted_at',
        'is_late',
        'grade',
        'points_earned',
        'instructor_feedback',
        'graded_by',
        'graded_at',
        'grade_metadata',
        'attempt_number'
    ];

    protected $casts = [
        'file_paths' => 'array',
        'file_metadata' => 'array',
        'grade_metadata' => 'array',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'is_late' => 'boolean',
        'grade' => 'decimal:2'
    ];

    /**
     * Relationships
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Scopes
     */
    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('submitted_at');
    }

    public function scopeGraded($query)
    {
        return $query->whereNotNull('grade');
    }

    public function scopeLate($query)
    {
        return $query->where('is_late', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Helper methods
     */
    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    public function isGraded(): bool
    {
        return $this->grade !== null;
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function canEdit(): bool
    {
        return $this->isDraft() && $this->assignment->canSubmit();
    }

    public function submit(): bool
    {
        if (!$this->assignment->canSubmit()) {
            return false;
        }

        $this->status = 'submitted';
        $this->submitted_at = now();
        $this->is_late = $this->assignment->isOverdue();

        return $this->save();
    }

    public function getFileUrls(): array
    {
        if (!$this->file_paths) {
            return [];
        }

        $urls = [];
        foreach ($this->file_paths as $path) {
            $urls[] = Storage::disk('public')->url($path);
        }

        return $urls;
    }

    public function getFileDownloadUrls(): array
    {
        if (!$this->file_paths) {
            return [];
        }

        $urls = [];
        foreach ($this->file_paths as $index => $path) {
            $urls[$index] = [
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
                'name' => $this->file_metadata[$index]['original_name'] ?? basename($path),
                'size' => $this->file_metadata[$index]['size'] ?? 0,
                'type' => $this->file_metadata[$index]['mime_type'] ?? 'application/octet-stream'
            ];
        }

        return $urls;
    }

    public function getTotalFileSize(): int
    {
        if (!$this->file_metadata) {
            return 0;
        }

        return array_sum(array_column($this->file_metadata, 'size'));
    }

    public function getFormattedFileSize(): string
    {
        $bytes = $this->getTotalFileSize();
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        return number_format($bytes / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

    public function getStatusBadgeClass(): string
    {
        switch ($this->status) {
            case 'draft':
                return 'bg-gray-100 text-gray-800';
            case 'submitted':
                return $this->is_late ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800';
            case 'late':
                return 'bg-orange-100 text-orange-800';
            case 'graded':
                return 'bg-green-100 text-green-800';
            case 'returned':
                return 'bg-purple-100 text-purple-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    public function getStatusText(): string
    {
        switch ($this->status) {
            case 'draft':
                return 'Draft';
            case 'submitted':
                return $this->is_late ? 'Submitted (Late)' : 'Submitted';
            case 'late':
                return 'Late Submission';
            case 'graded':
                return 'Graded';
            case 'returned':
                return 'Returned';
            default:
                return 'Unknown';
        }
    }

    public function getGradePercentage(): ?float
    {
        if (!$this->isGraded() || !$this->assignment->max_points) {
            return null;
        }

        return ($this->points_earned / $this->assignment->max_points) * 100;
    }

    public function getLetterGrade(): ?string
    {
        $percentage = $this->getGradePercentage();
        if ($percentage === null) {
            return null;
        }

        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    public function deleteFiles(): void
    {
        if ($this->file_paths) {
            foreach ($this->file_paths as $path) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($submission) {
            $submission->deleteFiles();
        });
    }
}
