<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Duplicateable; // Import Trait

class Course extends Model
{
    use HasFactory, Duplicateable; // Gunakan Trait

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'objectives',
        'thumbnail',
        'status',
    ];

    /**
     * Define which relations to duplicate.
     * We duplicate lessons, instructors, and event organizers.
     * We DO NOT duplicate participants/enrolledUsers.
     * @var array
     */
    protected $duplicateRelations = ['lessons', 'instructors', 'eventOrganizers'];
    
    /**
     * Define which attribute contains a file to be duplicated.
     * @var string
     */
    protected $replicateFile = 'thumbnail';


    // Relasi ke User (instruktur yang membuat kursus)
    public function instructors()
    {
        return $this->belongsToMany(User::class, 'course_instructor');
    }

     // Relasi ke Lesson (satu kursus punya banyak pelajaran)
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    // Relasi ke User (peserta yang enroll kursus ini)
    public function participants()
    {
        return $this->belongsToMany(User::class, 'course_user');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function enrolledUsers()
    {
        return $this->belongsToMany(User::class, 'course_user')->withPivot('feedback')->withTimestamps();
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function eventOrganizers()
    {
        return $this->belongsToMany(User::class, 'course_event_organizer');
    }

    public function getAverageProgress(): int
    {
        $enrolledUsers = $this->enrolledUsers()->with('completedContents')->get();
        $totalUsers = $enrolledUsers->count();

        if ($totalUsers === 0) {
            return 0;
        }

        $totalProgressSum = 0;
        foreach ($enrolledUsers as $user) {
            $progressData = $user->getProgressForCourse($this);
            $totalProgressSum += $progressData['progress_percentage'];
        }

        return round($totalProgressSum / $totalUsers);
    }
}
