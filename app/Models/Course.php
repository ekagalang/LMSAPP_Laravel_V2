<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Quiz;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'objectives',
        'thumbnail',
        'status',
    ];

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
}