<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Duplicateable;
use Carbon\Carbon;

class Content extends Model
{
    use HasFactory, Duplicateable; // Gunakan Trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'type',
        'body',
        'file_path',
        'document_access_type',
        'order',
        'quiz_id',
        'scheduled_start',
        'scheduled_end',
        'is_scheduled',
        'timezone_offset',
        'scoring_enabled',
        'grading_mode',
        'requires_review',
        'is_optional',
        'attendance_required',
        'min_attendance_minutes',
        'attendance_notes',
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'is_scheduled' => 'boolean',
        'scoring_enabled' => 'boolean',
        'requires_review' => 'boolean',
        'is_optional' => 'boolean',
        'attendance_required' => 'boolean',
        'min_attendance_minutes' => 'integer',
    ];

    /**
     * Define which relations to duplicate.
     * If content has a quiz, the quiz itself should be duplicated.
     * @var array
     */
    protected $duplicateRelations = ['quiz'];

    /**
     * Define which attribute contains a file to be duplicated.
     * @var string
     */
    protected $replicateFile = 'file_path';

    public function isZoomAccessible(): bool
    {
        if ($this->type !== 'zoom' || !$this->is_scheduled) {
            return true; // Non-scheduled zoom or non-zoom content always accessible
        }

        $now = now();

        // Check if current time is within scheduled window
        return $now->gte($this->scheduled_start) && $now->lte($this->scheduled_end);
    }

    public function getSchedulingStatus(): array
    {
        if ($this->type !== 'zoom' || !$this->is_scheduled) {
            return [
                'status' => 'available',
                'message' => 'Meeting tersedia',
                'can_join' => true
            ];
        }

        $now = now();

        if ($now->lt($this->scheduled_start)) {
            return [
                'status' => 'upcoming',
                'message' => 'Meeting akan dimulai pada ' . $this->scheduled_start->format('d M Y, H:i'),
                'can_join' => false,
                'starts_in' => $now->diffForHumans($this->scheduled_start)
            ];
        }

        if ($now->gt($this->scheduled_end)) {
            return [
                'status' => 'ended',
                'message' => 'Meeting telah berakhir pada ' . $this->scheduled_end->format('d M Y, H:i'),
                'can_join' => false,
                'ended_ago' => $this->scheduled_end->diffForHumans($now)
            ];
        }

        return [
            'status' => 'active',
            'message' => 'Meeting sedang berlangsung (berakhir ' . $this->scheduled_end->format('H:i') . ')',
            'can_join' => true,
            'ends_in' => $now->diffForHumans($this->scheduled_end)
        ];
    }

    public function getScheduledStartInTimezone($timezone = 'Asia/Jakarta'): ?Carbon
    {
        if (!$this->scheduled_start) return null;

        return $this->scheduled_start->setTimezone($timezone);
    }

    public function getScheduledEndInTimezone($timezone = 'Asia/Jakarta'): ?Carbon
    {
        if (!$this->scheduled_end) return null;

        return $this->scheduled_end->setTimezone($timezone);
    }

    /**
     * Relasi ke Lesson
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Multiple images attached to this content (for type === 'image').
     */
    public function images()
    {
        return $this->hasMany(ContentImage::class)->orderBy('order');
    }

    /**
     * Multiple documents attached to this content (for type === 'document').
     */
    public function documents()
    {
        return $this->hasMany(ContentDocument::class)->orderBy('order');
    }

    /**
     * Relasi ke Quiz (jika tipe kontennya adalah kuis)
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Relasi ke user yang menyelesaikan konten ini
     */
    public function completers()
    {
        return $this->belongsToMany(User::class, 'content_user')->withPivot('completed', 'completed_at');
    }

    /**
     * Get attendances for this content
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Check if attendance is required for this content
     */
    public function requiresAttendance(): bool
    {
        return $this->attendance_required ?? false;
    }

    /**
     * Get attendance statistics for this content
     */
    public function getAttendanceStats()
    {
        return [
            'total' => $this->attendances()->count(),
            'present' => $this->attendances()->where('status', 'present')->count(),
            'absent' => $this->attendances()->where('status', 'absent')->count(),
            'late' => $this->attendances()->where('status', 'late')->count(),
            'excused' => $this->attendances()->where('status', 'excused')->count(),
        ];
    }

    /**
     * Cek apakah konten ini sudah diselesaikan oleh user tertentu
     */
    public function isCompletedByUser($userId)
    {
        return $this->completers()->where('user_id', $userId)->wherePivot('completed', true)->exists();
    }

    /**
     * Relasi ke pengumpulan esai
     */
    public function essaySubmissions()
    {
        return $this->hasMany(EssaySubmission::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class)->orderBy('created_at', 'desc');
    }

    public function getYoutubeVideoIdAttribute(): ?string
    {
        if ($this->type !== 'video' || empty($this->body)) {
            return null;
        }
        // Regex ini akan mengekstrak ID dari hampir semua format URL YouTube
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $this->body, $match);
        return $match[1] ?? null;
    }

    /**
     * Accessor untuk URL embed YouTube.
     * @return string|null
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $videoId = $this->getYoutubeVideoIdAttribute();
        return $videoId ? 'https://www.youtube.com/embed/' . $videoId . '?autoplay=0&modestbranding=1&rel=0&color=white' : null;
    }

    /**
     * Accessor untuk URL thumbnail YouTube.
     * @return string|null
     */
    public function getYoutubeThumbnailUrlAttribute(): ?string
    {
        $videoId = $this->getYoutubeVideoIdAttribute();
        // Menggunakan thumbnail kualitas tinggi
        return $videoId ? 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg' : null;
    }

    public function essayQuestions()
    {
        return $this->hasMany(EssayQuestion::class)->where('is_active', true)->orderBy('order');
    }

    public function allEssayQuestions()
    {
        return $this->hasMany(EssayQuestion::class)->orderBy('order');
    }

    /**
     * Check apakah content ini adalah essay dengan multiple questions
     */
    public function hasMultipleQuestions()
    {
        return $this->type === 'essay' && $this->essayQuestions()->count() > 0;
    }

    /**
     * Get total max score untuk essay content
     */
    public function getEssayMaxScoreAttribute()
    {
        if ($this->type !== 'essay') {
            return 0;
        }
        return $this->essayQuestions->sum('max_score') ?: 100;
    }

    public function isIndividualGrading(): bool
    {
        return $this->grading_mode === 'individual';
    }

    public function isOverallGrading(): bool
    {
        return $this->grading_mode === 'overall';
    }

    /**
     * Check if scoring is enabled
     */
    public function isScoringEnabled(): bool
    {
        return $this->scoring_enabled ?? true;
    }
}
