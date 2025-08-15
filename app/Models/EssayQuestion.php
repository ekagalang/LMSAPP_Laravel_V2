<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EssayQuestion extends Model
{
    protected $fillable = [
        'content_id',
        'question',
        'order',
        'max_score'
    ];

    protected $casts = [
        'order' => 'integer',
        'max_score' => 'integer'
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function answers()
    {
        return $this->hasMany(EssayAnswer::class, 'question_id');
    }
}
