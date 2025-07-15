<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

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
        'role',
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
     * Relasi ke Course (kursus yang diajar oleh user ini sebagai instruktur)
     */
    public function instructedCourses()
    {
        return $this->belongsToMany(Course::class, 'course_instructor');
    }

    /**
     * Relasi ke Course (kursus yang diikuti oleh user ini sebagai peserta)
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user')->withPivot('feedback')->withTimestamps();
    }

    /**
     * Relasi ke Content yang sudah diselesaikan
     */
    public function completedContents()
    {
        return $this->belongsToMany(Content::class, 'content_user')->withPivot('completed', 'completed_at')->withTimestamps();
    }

    /**
     * Relasi ke Lesson yang sudah diselesaikan
     */
    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')->withPivot('completed', 'completed_at')->withTimestamps();
    }

    /**
     * Relasi ke Quiz yang dibuat (untuk instruktur)
     */
    public function createdQuizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Relasi ke QuizAttempt (percobaan kuis yang dilakukan user)
     */
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Relasi ke EssaySubmission (pengumpulan esai)
     */
    public function essaySubmissions()
    {
        return $this->hasMany(EssaySubmission::class);
    }

    /**
     * Relasi ke Discussion (diskusi yang dibuat user)
     */
    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    /**
     * Relasi ke DiscussionReply (balasan diskusi yang dibuat user)
     */
    public function discussionReplies()
    {
        return $this->hasMany(DiscussionReply::class);
    }

    /**
     * Relasi ke Feedback yang diterima user
     */
    public function receivedFeedback()
    {
        return $this->hasMany(Feedback::class, 'user_id');
    }

    /**
     * Relasi ke Feedback yang diberikan user (sebagai instruktur)
     */
    public function givenFeedback()
    {
        return $this->hasMany(Feedback::class, 'instructor_id');
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
     * Get user's role display name
     */
    public function getRoleDisplayName(): string
    {
        $roleNames = $this->getRoleNames();
        if ($roleNames->isEmpty()) {
            return 'Participant';
        }

        $role = $roleNames->first();
        return match ($role) {
            'super-admin' => 'Super Admin',
            'instructor' => 'Instruktur',
            'participant' => 'Peserta',
            'event-organizer' => 'Event Organizer',
            default => ucfirst($role),
        };
    }

    /**
     * Check if user is admin (super-admin)
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Check if user is instructor
     */
    public function isInstructor(): bool
    {
        return $this->hasRole('instructor');
    }

    /**
     * Check if user is participant
     */
    public function isParticipant(): bool
    {
        return $this->hasRole('participant');
    }

    /**
     * Check if user is event organizer
     */
    public function isEventOrganizer(): bool
    {
        return $this->hasRole('event-organizer');
    }

    /**
     * Get completion rate for a specific course
     */
    public function getCourseCompletionRate(Course $course): float
    {
        $totalContents = $course->lessons()
            ->with('contents')
            ->get()
            ->pluck('contents')
            ->flatten()
            ->count();

        if ($totalContents === 0) {
            return 0;
        }

        $completedContents = $this->completedContents()
            ->whereHas('lesson', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->count();

        return round(($completedContents / $totalContents) * 100, 2);
    }

    /**
     * Get user's recent activity
     */
    public function getRecentActivity($limit = 10)
    {
        $activities = collect();

        // Recent course enrollments
        $recentEnrollments = $this->courses()
            ->wherePivot('created_at', '>=', now()->subDays(30))
            ->get()
            ->map(function ($course) {
                return [
                    'type' => 'enrollment',
                    'description' => "Terdaftar di kursus: {$course->title}",
                    'created_at' => $course->pivot->created_at,
                ];
            });

        // Recent quiz attempts
        $recentQuizzes = $this->quizAttempts()
            ->with('quiz')
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->map(function ($attempt) {
                return [
                    'type' => 'quiz',
                    'description' => "Mengerjakan kuis: {$attempt->quiz->title}",
                    'created_at' => $attempt->created_at,
                ];
            });

        // Recent discussions
        $recentDiscussions = $this->discussions()
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->map(function ($discussion) {
                return [
                    'type' => 'discussion',
                    'description' => "Memulai diskusi: {$discussion->title}",
                    'created_at' => $discussion->created_at,
                ];
            });

        return $activities
            ->merge($recentEnrollments)
            ->merge($recentQuizzes)
            ->merge($recentDiscussions)
            ->sortByDesc('created_at')
            ->take($limit);
    }
}
