<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
    ];

    // Relasi ke Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Relasi ke Content (satu pelajaran punya banyak konten)
    public function contents()
    {
        return $this->hasMany(Content::class)->orderBy('order');
    }

    // Relasi ke user yang menyelesaikan pelajaran ini
    public function completers()
    {
        return $this->belongsToMany(User::class, 'lesson_user')->withPivot('completed', 'completed_at');
    }

    // Cek apakah pelajaran ini sudah diselesaikan oleh user tertentu
    public function isCompletedByUser($userId)
    {
        return $this->completers()->where('user_id', $userId)->wherePivot('completed', true)->exists();
    }
}