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
        // 'role', // Dihapus karena sudah digantikan oleh sistem Spatie
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
        return $this->belongsToMany(Course::class, 'course_user')->withPivot('feedback')->withTimestamps();
    }

    public function isEnrolled(Course $course)
    {
        return $this->enrolledCourses->contains($course);
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

    public function contents()
    {
        return $this->belongsToMany(Content::class, 'content_user')->withTimestamps();
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')->withTimestamps();
    }
}