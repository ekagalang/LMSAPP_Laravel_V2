<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Duplicateable; // Import Trait

class Lesson extends Model
{
    use HasFactory, Duplicateable; // Gunakan Trait

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'prerequisite_id', // Pastikan ini ada di $fillable
        'is_optional'
    ];
    
    protected $casts = [
        'is_optional' => 'boolean',
    ];
    
    /**
     * Define which relations to duplicate.
     * When a lesson is duplicated, its contents are also duplicated.
     * @var array
     */
    protected $duplicateRelations = ['contents'];

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
    
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function prerequisite()
    {
        return $this->belongsTo(Lesson::class, 'prerequisite_id');
    }
}
