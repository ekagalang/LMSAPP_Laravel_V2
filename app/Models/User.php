<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Feedback;
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
        // PENYESUAIAN: Menambahkan withPivot('feedback') untuk bisa mengambil data feedback
        return $this->belongsToMany(Course::class, 'course_user')->withTimestamps()->withPivot('feedback');
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
            ->withPivot('completed', 'completed_at') // Menggunakan kolom 'completed'
            ->withTimestamps();
    }

    public function contents()
    {
        return $this->completedContents();
    }

    public function getProgressForCourse($course)
    {
        $totalLessons = $course->lessons()->count();
        if ($totalLessons === 0) {
            return ['progress_percentage' => 100, 'completed_lessons' => 0, 'total_lessons' => 0];
        }

        $completedLessons = $this->progress()->whereHas('lesson', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })->where('completed', true)->count();

        return [
            'progress_percentage' => round(($completedLessons / $totalLessons) * 100),
            'completed_lessons' => $completedLessons,
            'total_lessons' => $totalLessons,
        ];
    }

    /**
     * Relasi ke lesson yang sudah diselesaikan
     */
    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class, 'progress')->wherePivot('completed', true);
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
        return $course->eventOrganizers()->where('user_id', $this->id)->exists();
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


    // Add these relationships to your existing User model
    // ========================================
    // 🆕 NEW CHAT RELATIONSHIPS
    // ========================================

    public function createdChats()
    {
        return $this->hasMany(Chat::class, 'created_by');
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_participants')
            ->withPivot(['joined_at', 'last_read_at', 'is_active'])
            ->withTimestamps();
    }

    public function activeChats()
    {
        return $this->chats()->wherePivot('is_active', true);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // ========================================
    // 🆕 NEW HELPER METHODS
    // ========================================

    /**
     * Check if user can chat with another user
     */
    public function canChatWith(User $targetUser): bool
    {
        // Admin can chat with anyone
        if ($this->hasRole('super-admin')) {
            return true;
        }

        // If admin initiated chat, others can reply
        $existingAdminChat = Chat::whereHas('participants', function ($q) {
            $q->where('user_id', $this->id);
        })->whereHas('participants', function ($q) use ($targetUser) {
            $q->where('user_id', $targetUser->id);
        })->whereHas('creator', function ($q) {
            $q->whereHas('roles', function ($r) {
                $r->where('name', 'super-admin');
            });
        })->first();

        if ($existingAdminChat) {
            return true;
        }

        // Check if both users are in the same active course period
        return $this->hasCommonActivePeriods($targetUser);
    }

    /**
     * Get common active course periods with another user
     */
    public function hasCommonActivePeriods(User $targetUser): bool
    {
        $myPeriods = $this->getActiveCoursePeriods();
        $targetPeriods = $targetUser->getActiveCoursePeriods();

        return $myPeriods->intersect($targetPeriods)->isNotEmpty();
    }

    /**
     * Get all active course periods for this user
     */
    public function getActiveCoursePeriods()
    {
        // Get enrolled course IDs
        $enrolledCourseIds = $this->courses()->pluck('course_id');

        // Get instructor course IDs
        $instructorCourseIds = $this->taughtCourses()->pluck('course_id');

        // Get event organizer course IDs
        $eventOrganizerCourseIds = $this->eventOrganizedCourses()->pluck('course_id');

        // Merge all course IDs
        $allCourseIds = $enrolledCourseIds->merge($instructorCourseIds)
            ->merge($eventOrganizerCourseIds)
            ->unique();

        // Get active periods for these courses
        return CoursePeriod::whereIn('course_id', $allCourseIds)
            ->active()
            ->pluck('id');
    }

    /**
     * Get available users to chat with
     */
    public function getAvailableChatUsers($search = null)
    {
        if ($this->hasRole('super-admin')) {
            // Admin can chat with anyone
            $query = User::where('id', '!=', $this->id);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            return $query->select('id', 'name', 'email')->limit(20)->get();
        }

        // Get users from common active course periods
        $activePeriods = $this->getActiveCoursePeriods();

        if ($activePeriods->isEmpty()) {
            return collect();
        }

        // Get all users who are enrolled, instructors, or event organizers in the same course periods
        $courseIds = CoursePeriod::whereIn('id', $activePeriods)->pluck('course_id')->unique();

        $query = User::where('id', '!=', $this->id)
            ->where(function ($q) use ($courseIds) {
                // Users enrolled in these courses
                $q->whereHas('courses', function ($sq) use ($courseIds) {
                    $sq->whereIn('course_id', $courseIds);
                })
                    // Users who are instructors for these courses
                    ->orWhereHas('taughtCourses', function ($sq) use ($courseIds) {
                        $sq->whereIn('course_id', $courseIds);
                    })
                    // Users who are event organizers for these courses
                    ->orWhereHas('eventOrganizedCourses', function ($sq) use ($courseIds) {
                        $sq->whereIn('course_id', $courseIds);
                    });
            });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->select('id', 'name', 'email')->limit(20)->get();
    }

    /**
     * Menghitung progres kursus untuk pengguna ini secara akurat.
     *
     * @param Course $course
     * @return float
     */
    public function courseProgress(Course $course): float
    {
        $allContents = $course->contents; // Mengambil semua konten dari relasi
        $totalContents = $allContents->count();

        if ($totalContents === 0) {
            return 0;
        }

        $completedCount = 0;
        foreach ($allContents as $content) {
            // Gunakan method hasCompletedContent() yang sudah ada
            // karena method tersebut sudah memeriksa semua jenis konten (biasa, kuis, esai)
            if ($this->hasCompletedContent($content)) {
                $completedCount++;
            }
        }

        return round(($completedCount / $totalContents) * 100, 2);
    }

    /**
     * Cek apakah semua item yang perlu dinilai sudah dinilai
     * PERBAIKAN: Menggunakan graded_at dan score, bukan status
     */
    public function areAllGradedItemsMarked(Course $course): bool
    {
        $gradedContentTypes = ['essay'];
        $itemsToGrade = $course->contents()->whereIn('type', $gradedContentTypes)->get();

        if ($itemsToGrade->isEmpty()) {
            return true; // Tidak ada essay, otomatis lolos
        }

        foreach ($itemsToGrade as $item) {
            if ($item->type == 'essay') {
                $submission = $this->essaySubmissions()->where('content_id', $item->id)->first();

                // PERBAIKAN: Cek graded_at dan score, bukan status
                if (
                    !$submission ||
                    is_null($submission->graded_at) ||
                    is_null($submission->score)
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    public function hasCompletedAllContentsInLesson(Lesson $lesson): bool
    {
        $totalContentsInLesson = $lesson->contents()->count();
        if ($totalContentsInLesson === 0) {
            return true;
        }

        $completedCountInLesson = $this->contents()
            ->where('lesson_id', $lesson->id)
            ->wherePivot('completed', true)
            ->count();

        return $completedCountInLesson >= $totalContentsInLesson;
    }

    /**
     * Memeriksa apakah sebuah lesson sudah ditandai selesai.
     */
    public function hasCompletedLesson(Lesson $lesson): bool
    {
        return $this->completedLessons()->where('lesson_id', $lesson->id)->wherePivot('status', 'completed')->exists();
    }

    // Add these relations and methods to your existing User model (App\Models\User.php)

    /**
     * Get all certificates earned by this user.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get certificates count for this user
     */
    public function getCertificatesCountAttribute(): int
    {
        return $this->certificates()->count();
    }

    /**
     * Get recent certificates (last 5)
     */
    public function getRecentCertificatesAttribute()
    {
        return $this->certificates()
            ->with(['course', 'certificateTemplate'])
            ->orderBy('issued_at', 'desc')
            ->take(5)
            ->get();
    }

    /**
     * Check if user has certificate for a specific course
     */
    public function hasCertificateForCourse(Course $course): bool
    {
        return \App\Models\Certificate::where('course_id', $course->id)
            ->where('user_id', $this->id)
            ->exists();
    }

    /**
     * Get certificate for a specific course
     */
    public function getCertificateForCourse(Course $course): ?\App\Models\Certificate
    {
        return \App\Models\Certificate::where('course_id', $course->id)
            ->where('user_id', $this->id)
            ->first();
    }

    /**
     * Cek apakah user eligible untuk sertifikat
     * PERBAIKAN: Membuat logika lebih fleksibel
     */
    public function isEligibleForCertificate(Course $course): bool
    {
        // Check if course has certificate template
        if (!$course->certificate_template_id) {
            return false;
        }

        // Check if enrolled
        if (!$this->courses()->where('course_id', $course->id)->exists()) {
            return false;
        }

        // Check progress 100%
        $progress = $this->courseProgress($course);
        if ($progress < 100) {
            return false;
        }

        // Check all graded items completed
        if (!$this->areAllGradedItemsMarked($course)) {
            return false;
        }

        return true;
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }
}
