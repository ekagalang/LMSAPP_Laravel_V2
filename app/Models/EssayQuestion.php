<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EssayQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'question',
        'order',
        'max_score'
    ];

    /**
     * Relasi ke Content
     */
    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * Relasi ke EssayAnswer
     */
    public function answers()
    {
        return $this->hasMany(EssayAnswer::class, 'question_id');
    }
}