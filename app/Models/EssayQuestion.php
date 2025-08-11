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
        'max_score',
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