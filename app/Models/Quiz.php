<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id', // Pertahankan ini untuk saat ini
        'user_id',
        'title',
        'description',
        'total_marks',
        'pass_marks',
        'show_answers_after_attempt',
        'time_limit',
        'status',
    ];

    // Relasi ke Lesson (satu kuis milik satu pelajaran)
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    // Relasi ke User (instruktur yang membuat kuis)
    public function instructor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Question (satu kuis punya banyak pertanyaan)
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // Relasi ke QuizAttempt (satu kuis punya banyak percobaan)
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // Relasi ke Content (satu kuis bisa menjadi konten dari satu pelajaran)
    public function content()
    {
        return $this->hasOne(Content::class); // Satu kuis bisa jadi konten dari satu pelajaran
    }
}