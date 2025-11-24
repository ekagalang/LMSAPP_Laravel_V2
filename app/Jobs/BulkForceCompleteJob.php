<?php

namespace App\Jobs;

use App\Http\Controllers\CertificateController;
use App\Models\Course;
use App\Models\User;
use App\Models\Content;
use App\Models\QuizAttempt;
use App\Models\EssaySubmission;
use App\Models\EssayAnswer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BulkForceCompleteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes per batch
    public $tries = 3;

    protected $course;
    protected $userIds;
    protected $generateCertificate;
    protected $batchId;

    /**
     * Create a new job instance.
     */
    public function __construct(Course $course, array $userIds, bool $generateCertificate = false, string $batchId = null)
    {
        $this->course = $course;
        $this->userIds = $userIds;
        $this->generateCertificate = $generateCertificate;
        $this->batchId = $batchId ?? uniqid('batch_', true);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Bulk Force Complete Job Started", [
            'batch_id' => $this->batchId,
            'course_id' => $this->course->id,
            'user_count' => count($this->userIds),
            'generate_cert' => $this->generateCertificate
        ]);

        $processed = 0;
        $errors = 0;

        foreach ($this->userIds as $userId) {
            try {
                $user = User::find($userId);
                if (!$user) {
                    Log::warning("User not found", ['user_id' => $userId, 'batch_id' => $this->batchId]);
                    continue;
                }

                DB::transaction(function () use ($user) {
                    $this->forceCompleteUserInCourse($user, $this->course);
                });

                // Generate certificate if requested
                if ($this->generateCertificate) {
                    try {
                        CertificateController::generateForUser($this->course, $user);
                        Log::info("Certificate generated", [
                            'user_id' => $user->id,
                            'course_id' => $this->course->id,
                            'batch_id' => $this->batchId
                        ]);
                    } catch (\Exception $e) {
                        Log::error("Certificate generation failed", [
                            'user_id' => $user->id,
                            'course_id' => $this->course->id,
                            'error' => $e->getMessage(),
                            'batch_id' => $this->batchId
                        ]);
                    }
                }

                $processed++;

                // Small delay to prevent overwhelming the system
                if ($processed % 10 === 0) {
                    usleep(100000); // 100ms delay every 10 users
                }

            } catch (\Exception $e) {
                $errors++;
                Log::error("Force complete failed for user", [
                    'user_id' => $userId,
                    'course_id' => $this->course->id,
                    'error' => $e->getMessage(),
                    'batch_id' => $this->batchId
                ]);
            }
        }

        Log::info("Bulk Force Complete Job Completed", [
            'batch_id' => $this->batchId,
            'processed' => $processed,
            'errors' => $errors,
            'total' => count($this->userIds)
        ]);
    }

    /**
     * Core logic: mark all contents completed for a user in a course
     */
    private function forceCompleteUserInCourse(User $user, Course $course): void
    {
        $lessons = $course->lessons()->with(['contents.quiz', 'contents.essayQuestions'])->get();

        foreach ($lessons as $lesson) {
            foreach ($lesson->contents as $content) {
                $this->completeContentForUser($user, $content);
            }
        }
    }

    private function completeContentForUser(User $user, Content $content): void
    {
        switch ($content->type) {
            case 'quiz':
                $this->forcePassQuiz($user, $content);
                break;
            case 'essay':
                $this->forceCompleteEssay($user, $content);
                break;
            default:
                // Mark generic content as completed via pivot
                $user->completedContents()->syncWithoutDetaching([
                    $content->id => [
                        'completed' => true,
                        'completed_at' => now(),
                    ],
                ]);
                break;
        }
    }

    private function forcePassQuiz(User $user, Content $content): void
    {
        if (!$content->quiz_id || !$content->quiz) return;

        // If user already has a passed attempt, skip
        $alreadyPassed = $user->quizAttempts()
            ->where('quiz_id', $content->quiz_id)
            ->where('passed', true)
            ->exists();

        if ($alreadyPassed) return;

        $quiz = $content->quiz;

        // ✅ FIX: Hitung pass_marks dari passing_percentage
        // ⚠️ CRITICAL FIX: Load questions jika belum ter-load
        if (!$quiz->relationLoaded('questions')) {
            $quiz->load('questions');
        }

        $totalMarks = $quiz->questions->sum('marks') ?: 100;
        $passingMarks = ($totalMarks * ($quiz->passing_percentage ?? 70)) / 100;

        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'score' => max($passingMarks, 0),
            'passed' => true,
            'started_at' => now(),
            'completed_at' => now(),
        ]);
    }

    private function forceCompleteEssay(User $user, Content $content): void
    {
        // Ensure submission exists
        $submission = EssaySubmission::firstOrCreate([
            'user_id' => $user->id,
            'content_id' => $content->id,
        ]);

        // Ensure answers exist
        $questions = $content->essayQuestions()->get();

        if ($questions->count() > 0) {
            foreach ($questions as $question) {
                EssayAnswer::firstOrCreate([
                    'submission_id' => $submission->id,
                    'question_id' => $question->id,
                ], [
                    'answer' => 'Completed offline due to incident',
                ]);
            }
        } else {
            if ($submission->answers()->count() === 0) {
                EssayAnswer::create([
                    'submission_id' => $submission->id,
                    'question_id' => null,
                    'answer' => 'Completed offline due to incident',
                ]);
            }
        }

        // Apply grading completion
        if ($content->requires_review === false) {
            $submission->update([
                'graded_at' => now(),
                'status' => 'reviewed',
            ]);
            return;
        }

        if ($content->scoring_enabled) {
            if ($content->grading_mode === 'overall') {
                $firstAnswer = $submission->answers()->first();
                if ($firstAnswer) {
                    $firstAnswer->update([
                        'score' => $firstAnswer->score ?? 0,
                        'feedback' => $firstAnswer->feedback ?? 'Force completed due to incident',
                    ]);
                }
            } else {
                foreach ($submission->answers as $answer) {
                    if ($answer->score === null) {
                        $answer->update([
                            'score' => 0,
                            'feedback' => $answer->feedback ?? 'Force completed due to incident',
                        ]);
                    }
                }
            }
        } else {
            if ($content->grading_mode === 'overall') {
                $firstAnswer = $submission->answers()->first();
                if ($firstAnswer && empty($firstAnswer->feedback)) {
                    $firstAnswer->update([
                        'feedback' => 'Force completed due to incident',
                    ]);
                }
            } else {
                foreach ($submission->answers as $answer) {
                    if (empty($answer->feedback)) {
                        $answer->update([
                            'feedback' => 'Force completed due to incident',
                        ]);
                    }
                }
            }
        }

        $submission->update([
            'graded_at' => now(),
            'status' => $content->scoring_enabled ? 'graded' : 'reviewed',
        ]);

        // Also mark the content as completed in pivot
        $user->completedContents()->syncWithoutDetaching([
            $content->id => [
                'completed' => true,
                'completed_at' => now(),
            ],
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Bulk Force Complete Job Failed", [
            'batch_id' => $this->batchId,
            'course_id' => $this->course->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
