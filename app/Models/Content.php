<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Duplicateable; // Import Trait

class Content extends Model
{
    use HasFactory, Duplicateable; // Gunakan Trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'type',
        'body',
        'file_path',
        'order',
        'quiz_id',
    ];

    /**
     * Define which relations to duplicate.
     * If content has a quiz, the quiz itself should be duplicated.
     * @var array
     */
    protected $duplicateRelations = ['quiz'];

    /**
     * Define which attribute contains a file to be duplicated.
     * @var string
     */
    protected $replicateFile = 'file_path';

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

    public function getYoutubeVideoIdAttribute(): ?string
    {
        if ($this->type !== 'video' || empty($this->body)) {
            return null;
        }
        // Regex ini akan mengekstrak ID dari hampir semua format URL YouTube
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $this->body, $match);
        return $match[1] ?? null;
    }

    /**
     * Accessor untuk URL embed YouTube.
     * @return string|null
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $videoId = $this->getYoutubeVideoIdAttribute();
        return $videoId ? 'https://www.youtube.com/embed/' . $videoId . '?autoplay=0&modestbranding=1&rel=0&color=white' : null;
    }

    /**
     * Accessor untuk URL thumbnail YouTube.
     * @return string|null
     */
    public function getYoutubeThumbnailUrlAttribute(): ?string
    {
        $videoId = $this->getYoutubeVideoIdAttribute();
        // Menggunakan thumbnail kualitas tinggi
        return $videoId ? 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg' : null;
    }

    public function essayQuestions()
    {
        return $this->hasMany(EssayQuestion::class);
    }

    /**
     * Check apakah content ini adalah essay dengan multiple questions
     */
    public function hasMultipleQuestions()
    {
        return $this->type === 'essay' && $this->essayQuestions()->count() > 0;
    }

    /**
     * Get total max score untuk essay content
     */
    public function getEssayMaxScoreAttribute()
    {
        if ($this->type !== 'essay') {
            return 0;
        }
        return $this->essayQuestions->sum('max_score') ?: 100;
    }
}
