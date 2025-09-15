<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Duplicateable;
use Carbon\Carbon;

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
        'scheduled_start',
        'scheduled_end',
        'is_scheduled',
        'timezone_offset',
        'scoring_enabled',
        'grading_mode',
        'requires_review',
        'audio_duration_seconds',
        'audio_difficulty_level',
        'audio_metadata',
        'is_audio_learning',
        'audio_lesson_id',
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'is_scheduled' => 'boolean',
        'scoring_enabled' => 'boolean',
        'requires_review' => 'boolean',
        'audio_metadata' => 'array',
        'is_audio_learning' => 'boolean',
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

    public function isZoomAccessible(): bool
    {
        if ($this->type !== 'zoom' || !$this->is_scheduled) {
            return true; // Non-scheduled zoom or non-zoom content always accessible
        }

        $now = now();

        // Check if current time is within scheduled window
        return $now->gte($this->scheduled_start) && $now->lte($this->scheduled_end);
    }

    public function getSchedulingStatus(): array
    {
        if ($this->type !== 'zoom' || !$this->is_scheduled) {
            return [
                'status' => 'available',
                'message' => 'Meeting tersedia',
                'can_join' => true
            ];
        }

        $now = now();

        if ($now->lt($this->scheduled_start)) {
            return [
                'status' => 'upcoming',
                'message' => 'Meeting akan dimulai pada ' . $this->scheduled_start->format('d M Y, H:i'),
                'can_join' => false,
                'starts_in' => $now->diffForHumans($this->scheduled_start)
            ];
        }

        if ($now->gt($this->scheduled_end)) {
            return [
                'status' => 'ended',
                'message' => 'Meeting telah berakhir pada ' . $this->scheduled_end->format('d M Y, H:i'),
                'can_join' => false,
                'ended_ago' => $this->scheduled_end->diffForHumans($now)
            ];
        }

        return [
            'status' => 'active',
            'message' => 'Meeting sedang berlangsung (berakhir ' . $this->scheduled_end->format('H:i') . ')',
            'can_join' => true,
            'ends_in' => $now->diffForHumans($this->scheduled_end)
        ];
    }

    public function getScheduledStartInTimezone($timezone = 'Asia/Jakarta'): ?Carbon
    {
        if (!$this->scheduled_start) return null;

        return $this->scheduled_start->setTimezone($timezone);
    }

    public function getScheduledEndInTimezone($timezone = 'Asia/Jakarta'): ?Carbon
    {
        if (!$this->scheduled_end) return null;

        return $this->scheduled_end->setTimezone($timezone);
    }

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

    /**
     * Relasi ke video interactions
     */
    public function videoInteractions()
    {
        return $this->hasMany(VideoInteraction::class)->active()->orderBy('timestamp');
    }

    /**
     * Get all video interactions for this content
     */
    public function getAllVideoInteractions()
    {
        return $this->hasMany(VideoInteraction::class)->orderBy('timestamp');
    }

    /**
     * Check if this content has interactive video features
     */
    public function hasInteractiveVideo()
    {
        return $this->type === 'video' && $this->videoInteractions()->count() > 0;
    }

    /**
     * Get video interactions by type
     */
    public function getVideoInteractionsByType($type)
    {
        return $this->videoInteractions()->byType($type)->get();
    }

    /**
     * Get user's responses to video interactions
     */
    public function getUserVideoResponses($userId)
    {
        return VideoInteractionResponse::whereHas('videoInteraction', function($query) {
            $query->where('content_id', $this->id);
        })->where('user_id', $userId)->get();
    }

    /**
     * Check if user has completed all required video interactions
     */
    public function hasUserCompletedVideoInteractions($userId)
    {
        $requiredInteractions = $this->videoInteractions()
            ->whereIn('type', ['quiz']) // Only quiz interactions are required
            ->count();
            
        if ($requiredInteractions === 0) {
            return true; // No required interactions
        }
        
        $completedInteractions = VideoInteractionResponse::whereHas('videoInteraction', function($query) {
                $query->where('content_id', $this->id)
                      ->whereIn('type', ['quiz']);
            })
            ->where('user_id', $userId)
            ->where('is_correct', true)
            ->count();
            
        return $completedInteractions >= $requiredInteractions;
    }

    /**
     * Get video interaction statistics for this content
     */
    public function getVideoInteractionStats()
    {
        $interactions = $this->videoInteractions;
        
        $stats = [
            'total_interactions' => $interactions->count(),
            'quiz_count' => $interactions->where('type', 'quiz')->count(),
            'annotation_count' => $interactions->where('type', 'annotation')->count(),
            'hotspot_count' => $interactions->where('type', 'hotspot')->count(),
            'overlay_count' => $interactions->where('type', 'overlay')->count(),
            'pause_count' => $interactions->where('type', 'pause')->count(),
            'total_responses' => 0,
            'average_success_rate' => 0
        ];
        
        if ($stats['total_interactions'] > 0) {
            $totalResponses = 0;
            $totalSuccessRate = 0;
            
            foreach ($interactions as $interaction) {
                $responses = $interaction->getTotalResponsesCount();
                $successRate = $interaction->getSuccessRate();
                
                $totalResponses += $responses;
                $totalSuccessRate += $successRate;
            }
            
            $stats['total_responses'] = $totalResponses;
            $stats['average_success_rate'] = $totalSuccessRate / $stats['total_interactions'];
        }
        
        return $stats;
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
        return $this->hasMany(EssayQuestion::class)->where('is_active', true)->orderBy('order');
    }

    public function allEssayQuestions()
    {
        return $this->hasMany(EssayQuestion::class)->orderBy('order');
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

    public function isIndividualGrading(): bool
    {
        return $this->grading_mode === 'individual';
    }

    public function isOverallGrading(): bool
    {
        return $this->grading_mode === 'overall';
    }

    /**
     * Check if scoring is enabled
     */
    public function isScoringEnabled(): bool
    {
        return $this->scoring_enabled ?? true;
    }

    /**
     * Get audio file URL
     */
    public function getAudioUrlAttribute(): ?string
    {
        if ($this->type === 'audio' && $this->file_path) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    /**
     * Get formatted audio duration
     */
    public function getFormattedAudioDurationAttribute(): string
    {
        if (!$this->audio_duration_seconds) return '00:00';

        $minutes = floor($this->audio_duration_seconds / 60);
        $seconds = $this->audio_duration_seconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Check if content is audio learning type
     */
    public function isAudioLearning(): bool
    {
        return $this->type === 'audio';
    }

    /**
     * Get audio difficulty badge color
     */
    public function getAudioDifficultyColorAttribute(): string
    {
        return match($this->audio_difficulty_level) {
            'beginner' => 'green',
            'intermediate' => 'yellow',
            'advanced' => 'red',
            default => 'gray'
        };
    }

    /**
     * Relation to AudioLesson for audio learning integration
     */
    public function audioLesson()
    {
        return $this->belongsTo(AudioLesson::class);
    }

    /**
     * Check if this content is linked to audio learning
     */
    public function isLinkedToAudioLearning(): bool
    {
        return $this->is_audio_learning && $this->audio_lesson_id;
    }
}
