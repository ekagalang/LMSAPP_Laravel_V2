<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'role', // Kolom role yang ditambahkan
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

    // ✅ RELASI UNTUK KURSUS

    /**
     * Kursus yang diajar oleh user ini (untuk instruktur)
     */
    public function instructedCourses()
    {
        return $this->belongsToMany(Course::class, 'course_instructor');
    }

    /**
     * Kursus yang diikuti oleh user ini (untuk peserta)
     * ✅ TAMBAHAN: Dengan pivot data completed_at dan feedback
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user')
            ->withPivot('feedback', 'completed_at')
            ->withTimestamps();
    }

    /**
     * ✅ BARU: Kursus yang sudah diselesaikan oleh user
     */
    public function completedCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user')
            ->withPivot('feedback', 'completed_at')
            ->whereNotNull('course_user.completed_at')
            ->withTimestamps();
    }

    // ✅ RELASI UNTUK KONTEN DAN PEMBELAJARAN

    /**
     * Pelajaran yang sudah diselesaikan oleh user
     */
    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')->withPivot('completed', 'completed_at');
    }

    /**
     * Konten yang sudah diselesaikan oleh user
     */
    public function completedContents()
    {
        return $this->belongsToMany(Content::class, 'content_user')->withPivot('completed', 'completed_at');
    }

    // ✅ RELASI UNTUK QUIZ DAN ESSAY

    /**
     * Quiz attempts oleh user
     */
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Essay submissions oleh user
     */
    public function essaySubmissions()
    {
        return $this->hasMany(EssaySubmission::class);
    }

    /**
     * Feedback yang diterima user
     */
    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    // ✅ HELPER METHODS

    /**
     * Cek apakah user sudah enroll di kursus tertentu
     */
    public function isEnrolled(Course $course): bool
    {
        return $this->courses()->where('course_id', $course->id)->exists();
    }

    /**
     * ✅ BARU: Cek apakah user sudah menyelesaikan kursus tertentu
     */
    public function hasCourseCompleted(Course $course): bool
    {
        return $this->courses()
            ->where('course_id', $course->id)
            ->whereNotNull('course_user.completed_at')
            ->exists();
    }

    /**
     * ✅ BARU: Mendapatkan tanggal completion kursus
     */
    public function getCourseCompletedAt(Course $course)
    {
        $pivot = $this->courses()
            ->where('course_id', $course->id)
            ->first();

        return $pivot ? $pivot->pivot->completed_at : null;
    }

    /**
     * ✅ BARU: Mendapatkan progress percentage untuk kursus tertentu
     */
    public function getCourseProgress(Course $course): array
    {
        // Ambil semua konten dalam kursus
        $allContents = $course->lessons()->with('contents')->get()->pluck('contents')->flatten();
        $totalContents = $allContents->count();

        if ($totalContents === 0) {
            return ['percentage' => 100, 'completed' => 0, 'total' => 0];
        }

        $completedCount = 0;

        foreach ($allContents as $content) {
            $isCompleted = false;

            if ($content->type === 'quiz' && $content->quiz_id) {
                $isCompleted = $this->quizAttempts()
                    ->where('quiz_id', $content->quiz_id)
                    ->where('passed', true)
                    ->exists();
            } elseif ($content->type === 'essay') {
                $isCompleted = $this->essaySubmissions()
                    ->where('content_id', $content->id)
                    ->exists();
            } else {
                $isCompleted = $this->completedContents()
                    ->where('content_id', $content->id)
                    ->exists();
            }

            if ($isCompleted) {
                $completedCount++;
            }
        }

        $percentage = round(($completedCount / $totalContents) * 100);

        return [
            'percentage' => $percentage,
            'completed' => $completedCount,
            'total' => $totalContents
        ];
    }

    /**
     * Cek apakah user adalah instruktur untuk kursus tertentu
     */
    public function isInstructorFor(Course $course): bool
    {
        return $course->instructors->contains($this);
    }

    /**
     * ✅ BARU: Mendapatkan semua kursus yang sudah diselesaikan dengan detail
     */
    public function getCompletedCoursesWithDetails()
    {
        return $this->completedCourses()->get()->map(function ($course) {
            $progress = $this->getCourseProgress($course);
            return [
                'course' => $course,
                'completed_at' => $course->pivot->completed_at,
                'progress' => $progress,
                'feedback' => $course->pivot->feedback
            ];
        });
    }

    /**
     * ✅ BARU: Mendapatkan kursus yang sedang dalam progress
     */
    public function getInProgressCourses()
    {
        return $this->courses()->whereNull('course_user.completed_at')->get()->map(function ($course) {
            $progress = $this->getCourseProgress($course);
            return [
                'course' => $course,
                'progress' => $progress,
                'feedback' => $course->pivot->feedback
            ];
        });
    }
}
