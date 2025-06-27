<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'score',
        'passed',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'passed' => 'boolean',
    ];

    // Relasi ke Quiz
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Relasi ke User (peserta yang mencoba kuis)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke QuestionAnswer (jawaban-jawaban dalam percobaan ini)
    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class);
    }
}