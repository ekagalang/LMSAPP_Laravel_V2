<?php

namespace App\Models;

// Pastikan ini adalah satu-satunya use statement untuk trait dari framework/package
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Quiz;

class User extends Authenticatable
{
    use HasFactory, Notifiable; // HANYA TRAIT INI SAJA UNTUK BREEZE BLADE

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Pastikan 'role' ada di sini
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- Metode Helper untuk Peran ---

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isInstructor()
    {
        return $this->role === 'instructor';
    }

    public function isParticipant()
    {
        return $this->role === 'participant';
    }

    // --- Relasi (akan digunakan di tahap selanjutnya) ---
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // Relasi untuk kursus yang diikuti (sebagai peserta)
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user');
    }

    // Helper untuk mengecek apakah user sudah enroll kursus tertentu
    public function isEnrolled(Course $course)
    {
        return $this->enrolledCourses->contains($course);
    }

    // Relasi ke pelajaran yang diselesaikan
    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')->withPivot('completed', 'completed_at');
    }

    // Relasi ke konten yang diselesaikan
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
}