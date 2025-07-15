<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'instructor_id',
        'feedback',
    ];

    /**
     * Relasi ke Course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Relasi ke User (peserta yang menerima feedback)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke User (instruktur yang memberikan feedback)
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
