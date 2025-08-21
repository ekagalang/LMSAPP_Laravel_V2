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
        if ($this->answers->count() === 0) return false;

        $totalQuestions = $this->content->essayQuestions()->count();
        $scoringEnabled = $this->content->scoring_enabled ?? true;
        $gradingMode = $this->content->grading_mode ?? 'individual';
        
        // ✅ PERBAIKAN: Handle mode overall
        if ($gradingMode === 'overall') {
            if ($scoringEnabled) {
                // Overall + Scoring: Cek apakah ada nilai di answer pertama
                $firstAnswer = $this->answers()->first();
                return $firstAnswer && $firstAnswer->score !== null;
            } else {
                // Overall + Feedback Only: Cek apakah ada feedback di answer pertama
                $firstAnswer = $this->answers()->first();
                return $firstAnswer && !empty($firstAnswer->feedback);
            }
        }
        
        // Mode individual (logic lama)
        if ($totalQuestions === 0) {
            // Legacy essay tanpa questions
            if ($scoringEnabled) {
                return $this->answers()->whereNotNull('score')->count() > 0;
            } else {
                return $this->answers()->whereNotNull('feedback')->count() > 0;
            }
        }
        
        // Individual mode dengan multiple questions
        if ($scoringEnabled) {
            $gradedAnswers = $this->answers()->whereNotNull('score')->count();
            return $gradedAnswers >= $totalQuestions;
        } else {
            $feedbackAnswers = $this->answers()->whereNotNull('feedback')->count();
            return $feedbackAnswers >= $totalQuestions;
        }
    }

    public function getCompletionStatusDetailAttribute()
    {
        $totalQuestions = $this->content->essayQuestions()->count();
        $scoringEnabled = $this->content->scoring_enabled ?? true;
        $gradingMode = $this->content->grading_mode ?? 'individual';
        
        $firstAnswer = $this->answers()->first();
        $answersWithScore = $this->answers()->whereNotNull('score')->count();
        $answersWithFeedback = $this->answers()->whereNotNull('feedback')->count();
        
        return [
            'total_questions' => $totalQuestions,
            'scoring_enabled' => $scoringEnabled,
            'grading_mode' => $gradingMode,
            'total_answers' => $this->answers->count(),
            'answers_with_score' => $answersWithScore,
            'answers_with_feedback' => $answersWithFeedback,
            'first_answer_has_score' => $firstAnswer && $firstAnswer->score !== null,
            'first_answer_has_feedback' => $firstAnswer && !empty($firstAnswer->feedback),
            'is_fully_graded' => $this->is_fully_graded,
            'graded_at' => $this->graded_at,
            'status' => $this->status
        ];
    }


    public function getCompletionStatusAttribute()
    {
        $totalQuestions = $this->content->essayQuestions()->count();
        
        if ($totalQuestions === 0) {
            // Legacy model: complete when submitted
            return [
                'type' => 'legacy',
                'is_submitted' => $this->answers()->count() > 0,
                'is_graded' => $this->answers()->whereNotNull('score')->count() > 0,
                'is_complete_for_participant' => $this->answers()->count() > 0, // Participant: cukup submit
                'is_complete_for_progress' => $this->answers()->whereNotNull('score')->count() > 0 // Progress: perlu graded
            ];
        } else {
            // Multi-question model: complete when all graded
            $gradedAnswers = $this->answers()->whereNotNull('score')->count();
            $submittedAnswers = $this->answers()->whereNotNull('answer')->count();
            
            return [
                'type' => 'multi_question',
                'total_questions' => $totalQuestions,
                'submitted_answers' => $submittedAnswers,
                'graded_answers' => $gradedAnswers,
                'is_submitted' => $submittedAnswers >= $totalQuestions,
                'is_graded' => $gradedAnswers >= $totalQuestions,
                'is_complete_for_participant' => $submittedAnswers >= $totalQuestions, // Participant: cukup submit semua
                'is_complete_for_progress' => $gradedAnswers >= $totalQuestions // Progress: perlu semua graded
            ];
        }
    }

    public function canUnlockNextContent(): bool
    {
        $status = $this->completion_status;
        
        // Untuk unlock content berikutnya, participant cukup sudah submit
        return $status['is_complete_for_participant'];
    }

    public function isCompleteForProgress(): bool
    {
        $status = $this->completion_status;
        
        // Untuk progress tracking, perlu sudah graded (kecuali legacy yang tidak perlu grading)
        if ($status['type'] === 'legacy') {
            // Legacy: complete when submitted (backward compatibility)
            return $status['is_submitted'];
        }
        
        // Multi-question: complete when fully graded
        return $status['is_complete_for_progress'];
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
