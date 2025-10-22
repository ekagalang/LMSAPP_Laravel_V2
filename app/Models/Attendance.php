<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content_id',
        'course_id',
        'joined_at',
        'left_at',
        'duration_minutes',
        'status',
        'notes',
        'marked_by',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    /**
     * Get the user that this attendance belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the content that this attendance is for
     */
    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * Get the course that this attendance is for
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Scope: Filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by content
     */
    public function scopeForContent($query, $contentId)
    {
        return $query->where('content_id', $contentId);
    }

    /**
     * Scope: Filter by course
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope: Only present attendances
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    /**
     * Check if attendance meets minimum duration requirement
     */
    public function meetsMinimumDuration()
    {
        if (!$this->content->attendance_required) {
            return true;
        }

        if (!$this->content->min_attendance_minutes) {
            return $this->status === 'present';
        }

        return $this->duration_minutes >= $this->content->min_attendance_minutes
            && $this->status === 'present';
    }

    /**
     * Calculate duration if joined_at and left_at are set
     */
    public function calculateDuration()
    {
        if ($this->joined_at && $this->left_at) {
            $this->duration_minutes = $this->joined_at->diffInMinutes($this->left_at);
            return $this->duration_minutes;
        }

        return 0;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'present' => 'green',
            'absent' => 'red',
            'late' => 'yellow',
            'excused' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'present' => 'Hadir',
            'absent' => 'Tidak Hadir',
            'late' => 'Terlambat',
            'excused' => 'Izin',
            default => 'Unknown',
        };
    }

    /**
     * Static method to mark attendance
     */
    public static function markAttendance($userId, $contentId, $status = 'present', $data = [])
    {
        $content = Content::findOrFail($contentId);

        $attendance = static::updateOrCreate(
            [
                'user_id' => $userId,
                'content_id' => $contentId,
            ],
            [
                'course_id' => $content->lesson->course_id,
                'status' => $status,
                'joined_at' => $data['joined_at'] ?? now(),
                'left_at' => $data['left_at'] ?? null,
                'duration_minutes' => $data['duration_minutes'] ?? 0,
                'notes' => $data['notes'] ?? null,
                'marked_by' => $data['marked_by'] ?? auth()->user()?->name ?? 'system',
            ]
        );

        return $attendance;
    }
}
