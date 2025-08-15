<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EssaySubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content_id',
        'status',
        'graded_at',
    ];

    protected $casts = [
        'graded_at' => 'datetime',
    ];

    /**
     * Mendapatkan pengguna yang mengirimkan esai.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan konten esai yang terkait.
     */
    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function essayAnswers()
    {
        return $this->hasMany(EssayAnswer::class, 'submission_id');
    }

    /**
     * Relasi ke EssayAnswer (jawaban per pertanyaan)
     */
    public function answers()
    {
        return $this->hasMany(EssayAnswer::class, 'submission_id');
    }


    /**
     * Hitung total skor dari semua jawaban
     */
    public function getTotalScoreAttribute()
    {
        return $this->answers()->whereNotNull('score')->avg('score') ?? 0;
    }

    /**
     * Hitung skor maksimal yang bisa didapat
     */
    public function getMaxTotalScoreAttribute()
    {
        return $this->content->essayQuestions->sum('max_score') ?: 100;
    }

    /**
     * Check apakah sudah dinilai semua pertanyaan
     */
    public function getIsFullyGradedAttribute()
    {
        if ($this->answers->count() === 0) return false;

        $totalQuestions = $this->content->essayQuestions()->count();
        $gradedAnswers = $this->answers()->whereNotNull('score')->count();

        return $gradedAnswers === $totalQuestions;
    }

    /**
     * Accessor untuk backward compatibility - total skor
     
    public function getScoreAttribute()
    {
        return $this->total_score;
    }
     */

    /**
     * Accessor untuk backward compatibility - feedback gabungan
     */
    public function getFeedbackAttribute()
    {
        return $this->answers->pluck('feedback')->filter()->implode(' | ');
    }

    /**
     * Accessor untuk backward compatibility - answer gabungan
     */
    public function getAnswerAttribute()
    {
        return $this->answers->pluck('answer')->implode('<hr>');
    }
}
