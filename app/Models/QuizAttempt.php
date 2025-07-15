<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class QuizAttempt extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quiz_id',
        'user_id',
        'score',
        'passed',
        'started_at',
        'completed_at',
    ];

    /**
     * The attributes that should be cast to native types.
     * INI YANG PENTING! 🔥
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime', // ← TAMBAHKAN INI
        'passed' => 'boolean',
        'score' => 'integer',
    ];

    /**
     * Relasi ke Quiz
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke jawaban-jawaban
     */
    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class, 'quiz_attempt_id');
    }

    /**
     * Accessor untuk mendapatkan waktu selesai yang diformat
     */
    public function getFormattedCompletedAtAttribute()
    {
        if (!$this->completed_at) {
            return 'Belum selesai';
        }

        return $this->completed_at->format('d M Y, H:i');
    }

    /**
     * Accessor untuk mendapatkan durasi pengerjaan
     */
    public function getDurationAttribute()
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return $this->completed_at->diffForHumans($this->started_at, true);
    }
}
