<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Duplicateable; // Import Trait
use App\Services\TokenGenerator;

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
        'enrollment_token',
        'token_enabled',
        'token_expires_at',
        'token_type',
    ];

    protected $casts = [
        'token_enabled' => 'boolean',
        'token_expires_at' => 'datetime',
    ];

    /**
     * Define which relations to duplicate.
     * PERBAIKAN: Hanya duplikasi lessons, TIDAK duplikasi users (instructors, eventOrganizers, participants)
     * agar course duplikat tidak membawa data user dari course asli.
     * Ini untuk kelas/batch baru dengan peserta baru.
     * @var array
     */
    protected $duplicateRelations = ['lessons'];

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
     * Course classes - one course can have multiple classes
     */
    public function classes()
    {
        return $this->hasMany(CourseClass::class)->orderBy('start_date');
    }

    // Alias for backward compatibility
    public function periods()
    {
        return $this->classes();
    }

    /**
     * Currently active class
     */
    public function activeClass()
    {
        return $this->hasOne(CourseClass::class)->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    // Alias for backward compatibility
    public function activePeriod()
    {
        return $this->activeClass();
    }

    /**
     * All chats related to this course through classes
     */
    public function chats()
    {
        return $this->hasManyThrough(Chat::class, CourseClass::class);
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
     * Check if user has any role in this course (legacy method - checks course level only)
     */
    public function hasUser($userId): bool
    {
        return  $this->enrolledUsers()->where('users.id', $userId)->exists() ||
            $this->instructors()->where('users.id', $userId)->exists() ||
            $this->eventOrganizers()->where('users.id', $userId)->exists();
    }

    /**
     * Check if user has access to any period in this course
     */
    public function hasUserInAnyPeriod($userId): bool
    {
        return $this->periods()->whereHas('participants', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })->exists() || 
        $this->periods()->whereHas('instructors', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })->exists() ||
        $this->eventOrganizers()->where('users.id', $userId)->exists();
    }

    /**
     * Get periods where user is enrolled as participant
     */
    public function getUserPeriods($userId)
    {
        return $this->periods()->whereHas('participants', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })->get();
    }

    /**
     * Get periods where user is assigned as instructor
     */
    public function getUserInstructorPeriods($userId)
    {
        return $this->periods()->whereHas('instructors', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })->get();
    }

    /**
     * Get all participants across all periods
     */
    public function getAllPeriodParticipants()
    {
        $userIds = collect();
        
        foreach ($this->periods as $period) {
            $userIds = $userIds->merge($period->participants()->pluck('users.id'));
        }
        
        return User::whereIn('id', $userIds->unique())->get();
    }

    /**
     * Check if course uses period-based enrollment
     */
    public function usesPeriodEnrollment(): bool
    {
        return $this->periods()->exists();
    }

    /**
     * Get effective participants (period-based if available, otherwise course-level)
     */
    public function getEffectiveParticipants()
    {
        if ($this->usesPeriodEnrollment()) {
            return $this->getAllPeriodParticipants();
        }
        
        return $this->participants;
    }

    /**
     * Get active periods with available slots
     */
    public function getAvailablePeriods()
    {
        return $this->periods()->where('status', 'active')->get()->filter(function ($period) {
            return $period->hasAvailableSlots();
        });
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

    // ========================================
    // TOKEN METHODS
    // ========================================

    /**
     * Generate enrollment token (random or custom)
     *
     * @param string $type 'random' or 'custom'
     * @param string|null $customToken Token kustom jika type = 'custom'
     * @param int $length Panjang token jika type = 'random'
     * @param string $format Format token: 'alphanumeric', 'numeric', 'alpha'
     * @return array ['success' => bool, 'token' => string, 'message' => string]
     */
    public function generateEnrollmentToken(
        string $type = 'random',
        ?string $customToken = null,
        int $length = 8,
        string $format = 'alphanumeric'
    ): array {
        try {
            if ($type === 'custom') {
                if (empty($customToken)) {
                    return [
                        'success' => false,
                        'token' => '',
                        'message' => 'Custom token tidak boleh kosong'
                    ];
                }

                $validation = TokenGenerator::validateUniqueCustomToken(
                    static::class,
                    $customToken,
                    'enrollment_token',
                    $this->id
                );

                if (!$validation['valid']) {
                    return [
                        'success' => false,
                        'token' => $validation['token'],
                        'message' => $validation['message']
                    ];
                }

                $token = $validation['token'];
            } else {
                // Generate random token
                $token = TokenGenerator::generateUniqueRandom(
                    static::class,
                    $length,
                    $format,
                    'enrollment_token'
                );
            }

            $this->enrollment_token = $token;
            $this->token_type = $type;
            $this->token_enabled = true;
            $this->save();

            return [
                'success' => true,
                'token' => $token,
                'message' => 'Token berhasil dibuat'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'token' => '',
                'message' => 'Gagal membuat token: ' . $e->getMessage()
            ];
        }
    }

    public function disableToken(): void
    {
        $this->token_enabled = false;
        $this->save();
    }

    public function isTokenValid(): bool
    {
        if (!$this->token_enabled || !$this->enrollment_token) {
            return false;
        }

        if ($this->token_expires_at && $this->token_expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function validateToken(string $token): bool
    {
        return $this->enrollment_token === strtoupper($token) && $this->isTokenValid();
    }

    /**
     * Get token type label
     */
    public function getTokenTypeLabel(): string
    {
        return match($this->token_type) {
            'custom' => 'Custom',
            'random' => 'Random',
            default => 'Random',
        };
    }
}
