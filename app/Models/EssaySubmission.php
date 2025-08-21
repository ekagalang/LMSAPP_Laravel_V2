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
        if (!$this->content->scoring_enabled) {
            return null;
        }

        return $this->answers()->sum('score');
    }

    /**
     * Hitung skor maksimal yang bisa didapat
     */
    public function getMaxTotalScoreAttribute()
    {
        if (!$this->content->scoring_enabled) {
            return null;
        }

        return $this->answers()
            ->join('essay_questions', 'essay_answers.question_id', '=', 'essay_questions.id')
            ->sum('essay_questions.max_score');
    }

    /**
     * Check apakah sudah dinilai semua pertanyaan
     */
    public function getIsFullyGradedAttribute()
    {
        // Jika essay tidak memerlukan scoring, return true
        if (!$this->content->scoring_enabled) {
            return true;
        }

        // Untuk essay dengan scoring, cek apakah semua answers sudah ada score
        return $this->answers()
            ->whereNotNull('score')
            ->count() === $this->answers()->count();
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

    public function needsGrading()
    {
        return $this->content->scoring_enabled && !$this->is_fully_graded;
    }
}
