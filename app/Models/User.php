<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Keep for backward compatibility
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke kursus yang diikuti user (sebagai peserta)
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user')->withTimestamps();
    }

    /**
     * Alias untuk courses() - untuk konsistensi penamaan
     */
    public function enrolledCourses()
    {
        return $this->courses();
    }

    /**
     * Relasi ke kursus yang diajar user (sebagai instruktur)
     */
    public function taughtCourses()
    {
        return $this->belongsToMany(Course::class, 'course_instructor')->withTimestamps();
    }

    /**
     * Relasi ke kursus yang diorganisir user (sebagai event organizer)
     */
    public function eventOrganizedCourses()
    {
        return $this->belongsToMany(Course::class, 'course_event_organizer')->withTimestamps();
    }

    /**
     * Relasi ke konten yang sudah diselesaikan
     */
    public function completedContents()
    {
        return $this->belongsToMany(Content::class, 'content_user')
            ->withPivot('completed', 'completed_at')
            ->withTimestamps();
    }

    /**
     * Relasi ke lesson yang sudah diselesaikan
     */
    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')
            ->withPivot('completed', 'completed_at')
            ->withTimestamps();
    }

    /**
     * Relasi ke quiz attempts
     */
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Relasi ke essay submissions
     */
    public function essaySubmissions()
    {
        return $this->hasMany(EssaySubmission::class);
    }

    /**
     * Relasi ke diskusi yang dimulai user
     */
    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    /**
     * Relasi ke balasan diskusi
     */
    public function discussionReplies()
    {
        return $this->hasMany(DiscussionReply::class);
    }

    /**
     * Relasi ke feedback yang diberikan (sebagai instruktur)
     */
    public function givenFeedback()
    {
        return $this->hasMany(Feedback::class, 'instructor_id');
    }

    /**
     * Relasi ke feedback yang diterima (sebagai peserta)
     */
    public function receivedFeedback()
    {
        return $this->hasMany(Feedback::class, 'user_id');
    }

    /**
     * =================================================================
     * PENYESUAIAN UNTUK MEMPERBAIKI ERROR GRADEBOOK
     * =================================================================
     *
     * Menambahkan relasi 'feedback' yang merujuk ke 'receivedFeedback'.
     * Ini akan memperbaiki error "Call to undefined relationship [feedback]"
     * yang kemungkinan terjadi di GradebookController.
     */
    public function feedback()
    {
        return $this->receivedFeedback();
    }

    /**
     * Many-to-many relationship dengan announcements yang sudah dibaca
     */
    public function readAnnouncements(): BelongsToMany
    {
        return $this->belongsToMany(Announcement::class, 'announcement_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }



    /**
     * Check if user is enrolled in a specific course
     */
    public function isEnrolled(Course $course): bool
    {
        return $this->courses()->where('course_id', $course->id)->exists();
    }

    /**
     * Check if user is instructor for a specific course
     */
    public function isInstructorFor(Course $course): bool
    {
        return $course->instructors()->where('user_id', $this->id)->exists();
    }

    /**
     * Check if user is event organizer for a specific course
     */
    public function isEventOrganizerFor(Course $course): bool
    {
        return $this->hasRole('event-organizer');
    }

    /**
     * Get user's primary role for display
     */
    public function getPrimaryRoleAttribute(): string
    {
        $roles = $this->getRoleNames();

        if ($roles->contains('super-admin')) return 'Super Admin';
        if ($roles->contains('event-organizer')) return 'Event Organizer';
        if ($roles->contains('instructor')) return 'Instruktur';
        if ($roles->contains('participant')) return 'Peserta';

        return 'Pengguna';
    }

    /**
     * Get courses that user can manage (instructor + event organizer)
     */
    public function managedCourses()
    {
        if ($this->hasRole('super-admin')) {
            return Course::query();
        }

        if ($this->hasRole('event-organizer')) {
            return $this->eventOrganizedCourses();
        }

        if ($this->hasRole('instructor')) {
            return $this->taughtCourses();
        }

        return Course::whereRaw('1 = 0'); // Empty query
    }

    /**
     * Get announcements yang belum dibaca untuk user ini
     */
    public function getUnreadAnnouncementsAttribute()
    {
        return Announcement::unreadForUser($this)
            ->latest()
            ->take(10)
            ->get();
    }

    /**
     * Get jumlah announcements yang belum dibaca
     */
    public function getUnreadAnnouncementsCountAttribute(): int
    {
        return Announcement::unreadForUser($this)->count();
    }


    /**
     * Mark announcement sebagai read
     */
    public function markAnnouncementAsRead(Announcement $announcement)
    {
        return $announcement->markAsReadBy($this);
    }

    /**
     * Cek apakah announcement sudah dibaca
     */
    public function hasReadAnnouncement(Announcement $announcement): bool
    {
        return $this->readAnnouncements()
            ->where('announcement_id', $announcement->id)
            ->exists();
    }


    // 🚨 BARU: Helper method untuk mendapatkan recent announcement reads
    public function getRecentAnnouncementReadsAttribute()
    {
        return $this->readAnnouncements()
            ->with('announcement')
            ->latest('read_at')
            ->take(5)
            ->get();
    }

    /**
     * Get learning progress for a specific course
     */
    public function getProgressForCourse(Course $course): array
    {
        // 1. Dapatkan total konten dalam kursus dengan cara yang lebih efisien
        $totalContents = $course->lessons()->withCount('contents')->get()->sum('contents_count');

        if ($totalContents === 0) {
            return [
                'total_contents' => 0,
                'completed_contents' => 0,
                'progress_percentage' => 0,
            ];
        }

        // 2. Hitung konten biasa (non-tugas) yang selesai
        $completedRegularContents = $this->completedContents()
            ->whereHas('lesson', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            // Pastikan hanya menghitung tipe selain kuis dan esai
            ->whereNotIn('type', ['quiz', 'essay'])
            ->count();

        // 3. Hitung kuis yang telah LULUS (setiap kuis dihitung sekali)
        $passedQuizzes = $this->quizAttempts()
            ->where('passed', true)
            ->whereHas('quiz.lesson', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->select('quiz_id')
            ->distinct()
            ->get()
            ->count();

        // 4. Hitung esai yang telah DIKUMPULKAN (setiap esai dihitung sekali)
        $submittedEssays = $this->essaySubmissions()
            ->whereHas('content.lesson', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->select('content_id')
            ->distinct()
            ->get()
            ->count();

        // 5. Jumlahkan semua item yang selesai
        $totalCompleted = $completedRegularContents + $passedQuizzes + $submittedEssays;
        $progressPercentage = round(($totalCompleted / $totalContents) * 100);

        return [
            'total_contents' => $totalContents,
            'completed_contents' => $totalCompleted,
            'progress_percentage' => min(100, $progressPercentage), // Batasi maksimal 100%
        ];
    }

    /**
     * Scope for filtering users by role
     */
    public function scopeWithRole($query, string $role)
    {
        return $query->role($role);
    }

    /**
     * Scope for active users (you can define what "active" means)
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Get user's full name with role
     */
    public function getFullDisplayNameAttribute(): string
    {
        return $this->name . ' (' . $this->primary_role . ')';
    }

    /**
     * Check if user has completed a specific content
     */
    public function hasCompletedContent(Content $content): bool
    {
        if ($content->type === 'quiz' && $content->quiz_id) {
            return $this->quizAttempts()
                ->where('quiz_id', $content->quiz_id)
                ->where('passed', true)
                ->exists();
        }

        if ($content->type === 'essay') {
            return $this->essaySubmissions()
                ->where('content_id', $content->id)
                ->exists();
        }

        return $this->completedContents()
            ->where('content_id', $content->id)
            ->exists();
    }

    /**
     * Get upcoming deadlines for user
     */
    public function getUpcomingDeadlines()
    {
        // This would need to be implemented based on your deadline system
        // For now, return empty collection
        return collect();
    }

    /**
     * Get user's notification preferences
     */
    public function getNotificationPreferences(): array
    {
        // This could be stored in user preferences table
        // For now, return default preferences
        return [
            'email_notifications' => true,
            'in_app_notifications' => true,
            'assignment_reminders' => true,
            'course_updates' => true,
        ];
    }
}
