<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EssayAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'question_id',
        'answer',
        'score',
        'feedback'
    ];

    /**
     * Relasi ke EssaySubmission
     */
    public function submission()
    {
        return $this->belongsTo(EssaySubmission::class);
    }

    /**
     * Relasi ke EssayQuestion
     */
    public function question()
    {
        return $this->belongsTo(EssayQuestion::class);
    }
}