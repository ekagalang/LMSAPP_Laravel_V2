<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Duplicateable;

class Question extends Model
{
    use HasFactory, Duplicateable;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'type',
        'marks',
        'correct_answer',
        'alternative_answers',
        'comprehension_type',
        'expected_answer',
    ];

    // Relasi ke Quiz (satu pertanyaan milik satu kuis)
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Relasi ke Option (satu pertanyaan punya banyak pilihan jawaban)
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    // Relasi ke QuestionAnswer (satu pertanyaan bisa dijawab banyak kali)
    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class);
    }
}