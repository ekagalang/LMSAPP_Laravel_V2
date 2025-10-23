<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Feedback;
use App\Models\CourseClass;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Notifications\CustomResetPasswordNotification;

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
        'date_of_birth',
        'institution_name',
        'gender',
        'occupation',
        'phone',
        'monthly_income',
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
            'date_of_birth' => 'date',
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
     * Relasi ke kursus yang diajar (untuk instruktur)
     */
    public function instructorCourses()
    {
        return $this->belongsToMany(Course::class, 'course_instructor')->withTimestamps();
    }

    /**
     * Relasi ke kelas sebagai instruktur
     */
    public function instructorClasses()
    {
        return $this->belongsToMany(CourseClass::class, 'course_class_instructor');
    }

    // Alias backward compatibility
    public function instructorPeriods()
    {
        return $this->instructorClasses();
    }

    /**
     * Relasi ke kelas sebagai participant
     */
    public function participantClasses()
    {
        return $this->belongsToMany(CourseClass::class, 'course_class_user')->withPivot('feedback')->withTimestamps();
    }

    // Alias backward compatibility
    public function participantPeriods()
    {
        return $this->participantClasses();
    }

    /**
     * Get all classes where user is involved (either as instructor or participant)
     */
    public function allClasses()
    {
        $instructorClasses = $this->instructorClasses()->get();
        $participantClasses = $this->participantClasses()->get();
        return $instructorClasses->merge($participantClasses)->unique('id');
    }

    // Alias backward compatibility
    public function allPeriods()
    {
        return $this->allClasses();
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

    /**
     * Get attendances for this user
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getProgressForCourse(Course $course): array
    {
        // Get all contents from all lessons in this course
        $allContents = collect();
        foreach ($course->lessons as $lesson) {
            $allContents = $allContents->merge($lesson->contents);
        }

        $totalContents = $allContents->count();

        if ($totalContents === 0) {
            return [
                'progress_percentage' => 0,
                'completed_count' => 0,
                'total_count' => 0,
                'completed_contents' => 0 // For backward compatibility
            ];
        }

        $completedCount = 0;
        foreach ($allContents as $content) {
            if ($this->hasCompletedContent($content)) {
                $completedCount++;
            }
        }

        $progressPercentage = round(($completedCount / $totalContents) * 100, 2);

        return [
            'progress_percentage' => $progressPercentage,
            'completed_count' => $completedCount,
            'total_count' => $totalContents,
            'completed_contents' => $completedCount // For backward compatibility
        ];
    }

    /**
     * Relasi ke lesson yang sudah diselesaikan
     */
    public function completedLessons(): BelongsToMany
    {
        // PERBAIKAN: Mengganti 'completed' dengan 'status' sesuai skema database lesson_user
        return $this->belongsToMany(Lesson::class, 'lesson_user')
            ->withPivot('status', 'completed_at')
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
        if ($this->can('manage all courses')) {
            return Course::query();
        }
        if ($this->can('manage own courses')) {
            return $this->taughtCourses();
        }
        // Treat EO capabilities as managed for reporting views
        if ($this->can('view progress reports') || $this->can('view certificate management')) {
            return $this->eventOrganizedCourses();
        }
        return Course::whereRaw('1 = 0');
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
        if ($content->is_optional ?? false) {
            return $this->completedContents()
                ->where('content_id', $content->id)
                ->wherePivot('completed', true)
                ->exists();
        }

        // CHECK ATTENDANCE REQUIREMENT FOR SYNCHRONOUS CONTENT
        if ($content->attendance_required) {
            $attendance = $this->attendances()
                ->where('content_id', $content->id)
                ->first();

            if (!$attendance) {
                return false; // No attendance record = not completed
            }

            // Check if attendance meets minimum duration requirement
            if ($content->min_attendance_minutes && $attendance->duration_minutes < $content->min_attendance_minutes) {
                return false; // Didn't meet minimum duration
            }

            // Must be marked as present or excused
            if (!in_array($attendance->status, ['present', 'excused'])) {
                return false; // Absent or late = not completed
            }

            // Attendance requirement met, continue to check other completion criteria
        }

        if ($content->type === 'quiz' && $content->quiz_id) {
            return $this->quizAttempts()
                ->where('quiz_id', $content->quiz_id)
                ->where('passed', true)
                ->exists();
        } elseif ($content->type === 'essay') {
            $submission = $this->essaySubmissions()
                ->where('content_id', $content->id)
                ->first();

            if (!$submission) {
                return false;
            }

            // Gunakan logic yang sama dengan getContentStatus
            return $this->getContentStatus($content) === 'completed';
        } else {
            return $this->completedContents()
                ->where('content_id', $content->id)
                ->wherePivot('completed', true)
                ->exists();
        }
    }

    public function essayAnswers()
    {
        return $this->hasManyThrough(EssayAnswer::class, EssaySubmission::class, 'user_id', 'submission_id');
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

    public function courseClasses()
    {
        return $this->belongsToMany(CourseClass::class, 'course_class_user', 'user_id', 'course_class_id')
            ->withTimestamps();
    }

    // Alias for backward compatibility
    public function coursePeriods()
    {
        return $this->courseClasses();
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
    public function canChatWith(User $targetUser, $coursePeriodId = null): bool
    {
        // Cannot chat with self
        if ($this->id === $targetUser->id) {
            return false;
        }

        // Managers can always chat with anyone
        if ($this->can('manage all courses') || $targetUser->can('manage all courses')) {
            return true;
        }

        // If course period is specified, check course-specific permissions
        if ($coursePeriodId) {
            $coursePeriod = \App\Models\CourseClass::find($coursePeriodId);
            if (!$coursePeriod) {
                return false;
            }

            // Both users must be in this course period
            $thisUserInCourse = $this->isInCoursePeriod($coursePeriod);
            $targetUserInCourse = $targetUser->isInCoursePeriod($coursePeriod);

            return $thisUserInCourse && $targetUserInCourse;
        }

        // For general chat, allow if users share any course OR one is high-level role
        // Check if they share any course
        $sharedCourses = $this->getSharedCoursePeriods($targetUser);
        return $sharedCourses->isNotEmpty();
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
        return CourseClass::whereIn('course_id', $allCourseIds)
            ->active()
            ->pluck('id');
    }

    /**
     * Get available users to chat with
     */
    public function getAvailableChatUsers($search = null)
    {
        if ($this->can('manage all courses')) {
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
        $courseIds = CourseClass::whereIn('id', $activePeriods)->pluck('course_id')->unique();

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
        $progressData = $this->getProgressForCourse($course);

        return (float) ($progressData['progress_percentage'] ?? 0);
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
            return true;
        }

        foreach ($itemsToGrade as $item) {
            if ($item->is_optional ?? false) {
                // Konten opsional tidak menghalangi progres atau sertifikasi
                continue;
            }

            if ($item->type == 'essay') {
                $submission = $this->essaySubmissions()->where('content_id', $item->id)->first();

                if (!$submission) {
                    return false; // Belum submit
                }

                $totalQuestions = $item->essayQuestions()->count();

                if ($totalQuestions === 0) {
                    // Old system
                    if ($submission->answers()->count() === 0) {
                        return false;
                    }
                    continue;
                }

                // FITUR BARU: Jika essay tidak perlu review (latihan mandiri), langsung lanjut
                if (!($item->requires_review ?? true)) {
                    continue; // Skip validasi grading untuk latihan mandiri
                }

                // New system - check berdasarkan grading mode dan scoring
                if (!$item->scoring_enabled) {
                    // Tanpa scoring - cek feedback
                    if ($item->grading_mode === 'overall') {
                        if ($submission->answers()->whereNotNull('feedback')->count() === 0) {
                            return false;
                        }
                    } else {
                        if ($submission->answers()->whereNotNull('feedback')->count() < $totalQuestions) {
                            return false;
                        }
                    }
                } else {
                    // Dengan scoring - cek score
                    if ($item->grading_mode === 'overall') {
                        if ($submission->answers()->whereNotNull('score')->count() === 0) {
                            return false;
                        }
                    } else {
                        if ($submission->answers()->whereNotNull('score')->count() < $totalQuestions) {
                            return false;
                        }
                    }
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

    public function getContentStatus(Content $content): string
    {
        if ($content->is_optional ?? false) {
            return $this->completedContents()
                ->where('content_id', $content->id)
                ->wherePivot('completed', true)
                ->exists()
                ? 'completed'
                : 'not_started';
        }

        if ($content->type === 'quiz' && $content->quiz_id) {
            $passedAttempt = $this->quizAttempts()
                ->where('quiz_id', $content->quiz_id)
                ->where('passed', true)
                ->exists();

            if ($passedAttempt) {
                return 'completed';
            }

            $hasAttempt = $this->quizAttempts()
                ->where('quiz_id', $content->quiz_id)
                ->exists();

            return $hasAttempt ? 'failed' : 'not_started';
        } elseif ($content->type === 'essay') {
            $submission = $this->essaySubmissions()
                ->where('content_id', $content->id)
                ->first();

            if (!$submission) {
                return 'not_started';
            }

            $totalQuestions = $content->essayQuestions()->count();

            // Legacy system
            if ($totalQuestions === 0) {
                return $submission->answers()->count() > 0 ? 'completed' : 'not_started';
            }

            // FITUR BARU: Jika essay tidak perlu review (latihan mandiri), langsung completed
            if (!($content->requires_review ?? true)) {
                return $submission->answers()->count() > 0 ? 'completed' : 'not_started';
            }

            // New system - check berdasarkan scoring dan grading mode
            if (!$content->scoring_enabled) {
                // Tanpa scoring
                if ($content->grading_mode === 'overall') {
                    // Overall tanpa scoring
                    if ($submission->answers()->whereNotNull('feedback')->count() > 0) {
                        return 'completed';
                    } elseif ($submission->answers()->count() > 0) {
                        return 'pending_grade';
                    } else {
                        return 'not_started';
                    }
                } else {
                    // Individual tanpa scoring
                    $answersWithFeedback = $submission->answers()->whereNotNull('feedback')->count();
                    if ($answersWithFeedback >= $totalQuestions) {
                        return 'completed';
                    } elseif ($submission->answers()->count() > 0) {
                        return 'pending_grade';
                    } else {
                        return 'not_started';
                    }
                }
            } else {
                // Dengan scoring
                if ($content->grading_mode === 'overall') {
                    // Overall dengan scoring
                    if ($submission->answers()->whereNotNull('score')->count() > 0) {
                        return 'completed';
                    } elseif ($submission->answers()->count() > 0) {
                        return 'pending_grade';
                    } else {
                        return 'not_started';
                    }
                } else {
                    // Individual dengan scoring
                    $gradedAnswers = $submission->answers()->whereNotNull('score')->count();
                    if ($gradedAnswers >= $totalQuestions) {
                        return 'completed';
                    } elseif ($submission->answers()->count() > 0) {
                        return 'pending_grade';
                    } else {
                        return 'not_started';
                    }
                }
            }
        } else {
            return $this->completedContents()
                ->where('content_id', $content->id)
                ->wherePivot('completed', true)
                ->exists()
                ? 'completed'
                : 'not_started';
        }
    }

    public function getContentStatusText(Content $content): string
    {
        $status = $this->getContentStatus($content);

        switch ($status) {
            case 'completed':
                return 'Selesai';
            case 'pending_grade':
                return 'Menunggu Penilaian';
            case 'failed':
                return 'Belum Lulus';
            case 'not_started':
            default:
                return 'Belum Dimulai';
        }
    }

    public function getContentStatusBadgeClass(Content $content): string
    {
        $status = $this->getContentStatus($content);

        switch ($status) {
            case 'completed':
                return 'bg-green-100 text-green-800';
            case 'pending_grade':
                return 'bg-yellow-100 text-yellow-800';
            case 'failed':
                return 'bg-red-100 text-red-800';
            case 'not_started':
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Check if user is related to a course period (as participant, instructor, or organizer)
     */
    public function isInCoursePeriod($coursePeriod): bool
    {
        if (!$coursePeriod) {
            return false;
        }

        // Convert to model if ID is passed
        if (is_numeric($coursePeriod)) {
            $coursePeriod = \App\Models\CourseClass::find($coursePeriod);
            if (!$coursePeriod) {
                return false;
            }
        }

        // Managers always have access
        if ($this->can('manage all courses')) {
            return true;
        }

        // Check if user is instructor or organizer for this course
        $isInstructor = $coursePeriod->course->instructors()->where('user_id', $this->id)->exists();
        if ($isInstructor) {
            return true;
        }
        $isEventOrganizer = $coursePeriod->course->eventOrganizers()->where('user_id', $this->id)->exists();
        if ($isEventOrganizer) {
            return true;
        }

        // Check if user is participant in this course period
        $isParticipant = $coursePeriod->participants()->where('user_id', $this->id)->exists();
        return $isParticipant;
    }


    /**
     * Get shared course periods with another user
     */
    public function getSharedCoursePeriods(User $otherUser)
    {
        $myCoursePeriods = $this->getAccessibleCoursePeriods()->pluck('id');
        $otherCoursePeriods = $otherUser->getAccessibleCoursePeriods()->pluck('id');

        $sharedIds = $myCoursePeriods->intersect($otherCoursePeriods);

        return \App\Models\CourseClass::whereIn('id', $sharedIds)->with('course')->get();
    }

    /**
     * Check if user has access to course periods (for chat creation)
     */
    public function getAccessibleCourseClasses()
    {
        // Managers can see all course periods
        if ($this->can('manage all courses')) {
            return \App\Models\CourseClass::with('course')->get();
        }

        // Return all course classes related to user's courses (any role)
        $instructorCourseIds = $this->taughtCourses()->pluck('course_id');
        $eoCourseIds = $this->eventOrganizedCourses()->pluck('course_id');
        $enrolledCourseIds = $this->courses()->pluck('course_id');
        $allCourseIds = $instructorCourseIds->merge($eoCourseIds)->merge($enrolledCourseIds)->unique();

        return \App\Models\CourseClass::whereIn('course_id', $allCourseIds)
            ->with('course')
            ->get();
    }

    // Alias for backward compatibility
    public function getAccessibleCoursePeriods()
    {
        return $this->getAccessibleCourseClasses();
    }

    public function getAvailableUsersForChat($coursePeriodId = null, $search = null)
    {
        if ($this->can('manage all courses')) {
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

        // If specific course period is selected
        if ($coursePeriodId) {
            $coursePeriod = \App\Models\CourseClass::find($coursePeriodId);
            if (!$coursePeriod) {
                return collect();
            }

            // ✅ FIXED: Get ALL users related to this course period (any role)
            $course = $coursePeriod->course;

            $userIds = collect();

            // Get enrolled users (participants)
            $enrolledUserIds = $course->enrolledUsers()->pluck('user_id');
            $userIds = $userIds->merge($enrolledUserIds);

            // Get instructors
            $instructorUserIds = $course->instructors()->pluck('user_id');
            $userIds = $userIds->merge($instructorUserIds);

            // Get event organizers
            $organizerUserIds = $course->eventOrganizers()->pluck('user_id');
            $userIds = $userIds->merge($organizerUserIds);

            // ✅ NEW: Also include super-admins (they can join any chat)
            $adminUserIds = User::permission('manage all courses')->pluck('id');
            $userIds = $userIds->merge($adminUserIds);

            // Remove current user and get unique IDs
            $userIds = $userIds->unique()->reject(function ($id) {
                return $id == $this->id;
            });

            if ($userIds->isEmpty()) {
                return collect();
            }

            $query = User::whereIn('id', $userIds);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            return $query->select('id', 'name', 'email')->get();
        }

        // ✅ FIXED: For general chat, allow chat with users from any course + admins
        // Get all course IDs where current user has any role
        $courseIds = collect();

        // As participant
        $enrolledCourseIds = $this->courses()->pluck('course_id');
        $courseIds = $courseIds->merge($enrolledCourseIds);

        // As instructor  
        $instructorCourseIds = $this->taughtCourses()->pluck('course_id');
        $courseIds = $courseIds->merge($instructorCourseIds);

        // As event organizer
        $organizerCourseIds = $this->eventOrganizedCourses()->pluck('course_id');
        $courseIds = $courseIds->merge($organizerCourseIds);

        $courseIds = $courseIds->unique();

        if ($courseIds->isEmpty()) {
            // If user has no courses, only allow chat with admins
            $userIds = User::permission('manage all courses')->pluck('id');
        } else {
            // Get all users from user's courses
            $userIds = collect();

            foreach ($courseIds as $courseId) {
                $course = \App\Models\Course::find($courseId);
                if ($course) {
                    $courseUserIds = collect();
                    $courseUserIds = $courseUserIds->merge($course->enrolledUsers()->pluck('user_id'));
                    $courseUserIds = $courseUserIds->merge($course->instructors()->pluck('user_id'));
                    $courseUserIds = $courseUserIds->merge($course->eventOrganizers()->pluck('user_id'));
                    $userIds = $userIds->merge($courseUserIds);
                }
            }

            // Also include super-admins
            $adminUserIds = User::permission('manage all courses')->pluck('id');
            $userIds = $userIds->merge($adminUserIds);
        }

        // Remove current user and get unique IDs
        $userIds = $userIds->unique()->reject(function ($id) {
            return $id == $this->id;
        });

        if ($userIds->isEmpty()) {
            return collect();
        }

        $query = User::whereIn('id', $userIds);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->select('id', 'name', 'email')->limit(20)->get();
    }

    public function sendPasswordResetNotification($token)
    {
        if (app()->environment('testing')) {
            $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));
        } else {
            $this->notify(new CustomResetPasswordNotification($token));
        }
    }
}
