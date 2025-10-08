<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Duplicateable;

class Quiz extends Model
{
    use HasFactory, Duplicateable;

    protected $fillable = [
        'lesson_id',
        'user_id',
        'title',
        'description',
        'passing_percentage',
        'show_answers_after_attempt',
        'time_limit',
        'enable_leaderboard',
        'status',
    ];

    // Relasi ke Lesson (satu kuis milik satu pelajaran)
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    // Relasi ke User (instruktur yang membuat kuis)
    public function instructor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Question (satu kuis punya banyak pertanyaan)
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // Relasi ke QuizAttempt (satu kuis punya banyak percobaan)
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // Relasi ke Content (satu kuis bisa menjadi konten dari satu pelajaran)
    public function content()
    {
        return $this->hasOne(Content::class); // Satu kuis bisa jadi konten dari satu pelajaran
    }

    /**
     * Get leaderboard data for this quiz
     * Ranked by: 1) Highest score, 2) Fastest completion time
     */
    public function leaderboard($limit = null)
    {
        $query = $this->attempts()
            ->with('user')
            ->whereNotNull('completed_at')
            ->orderByDesc('score')
            ->orderBy('completed_at');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get()->map(function ($attempt, $index) {
            return [
                'rank' => $index + 1,
                'user' => $attempt->user,
                'score' => $attempt->score,
                'percentage' => $attempt->percentage,
                'passed' => $attempt->passed,
                'completed_at' => $attempt->completed_at,
                'duration' => $attempt->duration,
            ];
        });
    }

    /**
     * Get user's best attempt for leaderboard
     */
    public function getLeaderboardWithBestAttempts($limit = null)
    {
        // Get all completed attempts grouped by user
        $attempts = $this->attempts()
            ->with('user')
            ->whereNotNull('completed_at')
            ->get()
            ->groupBy('user_id');

        // Get best attempt per user (highest score, then fastest)
        $bestAttempts = $attempts->map(function ($userAttempts) {
            return $userAttempts->sortByDesc('score')
                ->sortBy('completed_at')
                ->first();
        })->values();

        // Get total questions count for this quiz
        $totalQuestions = $this->questions()->count();

        // Sort and rank
        $ranked = $bestAttempts->sortByDesc('score')
            ->sortBy('completed_at')
            ->values()
            ->take($limit ?? $bestAttempts->count())
            ->map(function ($attempt, $index) use ($totalQuestions) {
                return [
                    'rank' => $index + 1,
                    'user' => $attempt->user,
                    'score' => $attempt->score,
                    'total_marks' => $totalQuestions,
                    'percentage' => $attempt->percentage,
                    'passed' => $attempt->passed,
                    'completed_at' => $attempt->completed_at,
                    'duration' => $attempt->duration,
                ];
            });

        return $ranked;
    }
}