<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EssayAnswer extends Model
{
    protected $fillable = [
        'submission_id',
        'question_id',
        'answer',
        'score',
        'feedback'
    ];

    protected $casts = [
        'score' => 'integer'
    ];

    public function submission()
    {
        return $this->belongsTo(EssaySubmission::class, 'submission_id');
    }

    public function question()
    {
        return $this->belongsTo(EssayQuestion::class, 'question_id');
    }
}
