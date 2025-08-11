<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Duplicateable; // Import Trait

class Course extends Model
{
    use HasFactory, Duplicateable; // Gunakan Trait

    protected $fillable = [
        'title',
        'description',
        'objectives',
        'thumbnail',
        'status',
        'certificate_template_id',
    ];

    /**
     * Define which relations to duplicate.
     * We duplicate lessons, instructors, and event organizers.
     * We DO NOT duplicate participants/enrolledUsers.
     * @var array
     */
    protected $duplicateRelations = ['lessons', 'instructors', 'eventOrganizers'];

    /**
     * Define which attribute contains a file to be duplicated.
     * @var string
     */
    protected $replicateFile = 'thumbnail';


    // Relasi ke User (instruktur yang membuat kursus)
    public function instructors()
    {
        return $this->belongsToMany(User::class, 'course_instructor');
    }

     // Relasi ke Lesson (satu kursus punya banyak pelajaran)
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    // Relasi ke User (peserta yang enroll kursus ini)
    public function participants()
    {
        return $this->belongsToMany(User::class, 'course_user');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function enrolledUsers()
    {
        return $this->belongsToMany(User::class, 'course_user')->withPivot('feedback')->withTimestamps();
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function eventOrganizers()
    {
        return $this->belongsToMany(User::class, 'course_event_organizer');
    }

    public function contents()
    {
        return $this->hasManyThrough(Content::class, Lesson::class)->select('contents.*');
    }

    public function getAverageProgress(): int
    {
        $enrolledUsers = $this->enrolledUsers()->with('completedContents')->get();
        $totalUsers = $enrolledUsers->count();

        if ($totalUsers === 0) {
            return 0;
        }

        $totalProgressSum = 0;
        foreach ($enrolledUsers as $user) {
            $progressData = $user->getProgressForCourse($this);
            $totalProgressSum += $progressData['progress_percentage'];
        }

        return round($totalProgressSum / $totalUsers);
    }

    // ========================================
    // 🆕 NEW RELATIONSHIPS FOR INTEGRATION
    // ========================================

    /**
     * Course periods - one course can have multiple periods/batches
     */
    public function periods()
    {
        return $this->hasMany(CoursePeriod::class)->orderBy('start_date');
    }

    /**
     * Currently active period
     */
    public function activePeriod()
    {
        return $this->hasOne(CoursePeriod::class)->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    /**
     * All chats related to this course through periods
     */
    public function chats()
    {
        return $this->hasManyThrough(Chat::class, CoursePeriod::class);
    }

    // ========================================
    // 🆕 NEW HELPER METHODS
    // ========================================

    /**
     * Check if course has any active period
     */
    public function hasActivePeriod(): bool
    {
        return $this->periods()->where('status', 'active')
                               ->where('start_date', '<=', now())
                               ->where('end_date', '>=', now())
                               ->exists();
    }

    /**
     * Get all users associated with this course (any role)
     */
    public function getAllUsers()
    {
        $enrolled = $this->enrolledUsers()->pluck('users.id');
        $instructors = $this->instructors()->pluck('users.id');
        $eventOrganizers = $this->eventOrganizers()->pluck('users.id');

        $allUserIds = $enrolled->merge($instructors)->merge($eventOrganizers)->unique();

        return User::whereIn('id', $allUserIds)->get();
    }

    /**
     * Check if user has any role in this course
     */
    public function hasUser($userId): bool
    {
        return  $this->enrolledUsers()->where('users.id', $userId)->exists() ||
                $this->instructors()->where('users.id', $userId)->exists() ||
                $this->eventOrganizers()->where('users.id', $userId)->exists();
    }

    /**
     * Get periods that are available for chat
     */
    public function activeChatPeriods()
    {
        return $this->periods()->whereIn('status', ['active'])->get();
    }

    // Add this relation to your existing Course model (App\Models\Course.php)

    /**
     * Get the certificate template for this course.
     */
    public function certificateTemplate()
    {
        return $this->belongsTo(CertificateTemplate::class);
    }

    /**
     * Get all certificates issued for this course.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Check if course has a certificate template assigned
     */
    public function hasCertificateTemplate(): bool
    {
        return !is_null($this->certificate_template_id);
    }

    /**
     * Get issued certificates count for this course
     */
    public function getIssuedCertificatesCountAttribute(): int
    {
        return $this->certificates()->count();
    }

    /**
     * Get eligible users count (users who completed the course)
     */
    public function getEligibleForCertificateCountAttribute(): int
    {
        $count = 0;
        foreach ($this->enrolledUsers as $user) {
            $progress = $user->courseProgress($this);
            $allGraded = $user->areAllGradedItemsMarked($this);

            if ($progress >= 100 && $allGraded) {
                $count++;
            }
        }
        return $count;
    }
}
