<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lesson_id',
        'title',
        'description', // Kolom baru ditambahkan
        'type',
        'body',
        'file_path',
        'order',
        'quiz_id',
    ];

    /**
     * Relasi ke Lesson
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Relasi ke Quiz (jika tipe kontennya adalah kuis)
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Relasi ke user yang menyelesaikan konten ini
     */
    public function completers()
    {
        return $this->belongsToMany(User::class, 'content_user')->withPivot('completed', 'completed_at');
    }

    /**
     * Cek apakah konten ini sudah diselesaikan oleh user tertentu
     */
    public function isCompletedByUser($userId)
    {
        return $this->completers()->where('user_id', $userId)->wherePivot('completed', true)->exists();
    }
    
    /**
     * Relasi ke pengumpulan esai
     */
    public function essaySubmissions()
    {
        return $this->hasMany(EssaySubmission::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class)->orderBy('created_at', 'desc');
    }
}
