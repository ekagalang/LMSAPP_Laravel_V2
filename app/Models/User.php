<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user')->withTimestamps();
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user')->withTimestamps();
    }

    public function taughtCourses()
    {
        return $this->belongsToMany(Course::class, 'course_instructor');
    }

    // ✅ FUNGSI isEnrolled DIUBAH MENJADI isEnrolledIn AGAR KONSISTEN & LEBIH EFISIEN
    /**
     * Memeriksa apakah pengguna terdaftar di kursus tertentu.
     */
    public function isEnrolledIn(Course $course): bool
    {
        return $this->enrolledCourses()->where('course_id', $course->id)->exists();
    }

    // ✅ FUNGSI BARU YANG HILANG SEBELUMNYA
    /**
     * Memeriksa apakah pengguna adalah instruktur untuk kursus tertentu.
     */
    public function isInstructorFor(Course $course): bool
    {
        // Mengecek ke tabel relasi 'course_instructor' apakah ada entri untuk user dan course ini.
        return $this->taughtCourses()->where('course_id', $course->id)->exists();
    }

    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')->withPivot('completed', 'completed_at');
    }

    public function completedContents()
    {
        return $this->belongsToMany(Content::class, 'content_user')->withPivot('completed', 'completed_at');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
    
    public function essaySubmissions(): HasMany
    {
        return $this->hasMany(EssaySubmission::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'user_id');
    }

    public function hasCompleted(Lesson $lesson): bool
    {
        // Pastikan relasi completers dimuat untuk efisiensi jika belum ada
        if (!$this->relationLoaded('completedLessons')) {
            $this->load('completedLessons');
        }
        return $this->completedLessons->contains($lesson);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    // Relasi ke balasan yang dibuat user
    public function discussionReplies()
    {
        return $this->hasMany(DiscussionReply::class);
    }
}