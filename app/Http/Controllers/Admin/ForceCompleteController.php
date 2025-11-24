<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CertificateController;
use App\Jobs\BulkForceCompleteJob;
use App\Jobs\BulkGenerateCertificatesJob;
use App\Models\Course;
use App\Models\User;
use App\Models\Content;
use App\Models\QuizAttempt;
use App\Models\EssaySubmission;
use App\Models\EssayAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForceCompleteController extends Controller
{
    /**
     * Show the Force Complete interface
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Course::class);

        // Ambil daftar kursus minimal (id, title) untuk dropdown agar ringan
        $courses = Course::query()->select('id', 'title')->orderBy('title')->get();
        $selectedCourse = null;
        $participants = collect();

        if ($request->has('course_id') && $request->course_id) {
            $selectedCourse = Course::with(['lessons.contents' => function ($q) {
                $q->select('id', 'lesson_id', 'type', 'quiz_id', 'scoring_enabled', 'grading_mode', 'requires_review', 'order');
            }])->find($request->course_id);

            if ($selectedCourse) {
                // Hitung konten kursus sekali
                $allContents = $selectedCourse->lessons->flatMap(function ($lesson) {
                    return $lesson->contents;
                });

                $totalContents = $allContents->count();
                $allContentIds = $allContents->pluck('id');
                $quizIds = $allContents->where('type', 'quiz')->pluck('quiz_id')->filter();
                $essayContentIds = $allContents->where('type', 'essay')->pluck('id');

                // Preload jumlah pertanyaan essay per content untuk evaluasi cepat
                $essayQuestionCounts = \App\Models\EssayQuestion::whereIn('content_id', $essayContentIds)
                    ->select('content_id', DB::raw('COUNT(*) as qcount'))
                    ->groupBy('content_id')
                    ->pluck('qcount', 'content_id');

                // Ambil peserta dengan eager loading data terkait kursus ini saja
                $participantsCollection = $selectedCourse->enrolledUsers()
                    ->select('users.id', 'users.name', 'users.email')
                    ->with([
                        'completedContents' => function ($q) use ($allContentIds) {
                            $q->whereIn('content_id', $allContentIds);
                        },
                        'quizAttempts' => function ($q) use ($quizIds) {
                            if ($quizIds->isNotEmpty()) {
                                $q->whereIn('quiz_id', $quizIds);
                            } else {
                                $q->whereRaw('1=0');
                            }
                        },
                        'essaySubmissions' => function ($q) use ($essayContentIds) {
                            if ($essayContentIds->isNotEmpty()) {
                                $q->whereIn('content_id', $essayContentIds)
                                  ->with(['answers:id,submission_id,question_id,score,feedback', 'content:id,scoring_enabled,grading_mode,requires_review']);
                            } else {
                                $q->whereRaw('1=0');
                            }
                        },
                    ])
                    ->get();

                // Hitung progres per peserta tanpa N+1 query
                $participants = $participantsCollection->map(function ($user) use ($allContents, $totalContents, $essayQuestionCounts) {
                    $completed = 0;

                    foreach ($allContents as $content) {
                        $isCompleted = false;

                        if ($content->type === 'quiz' && $content->quiz_id) {
                            $isCompleted = $user->quizAttempts->firstWhere(function ($att) use ($content) {
                                return $att->quiz_id == $content->quiz_id && $att->passed === true;
                            }) ? true : false;
                        } elseif ($content->type === 'essay') {
                            $submission = $user->essaySubmissions->firstWhere('content_id', $content->id);
                            if ($submission) {
                                $answers = $submission->answers;
                                $totalQuestions = (int)($essayQuestionCounts[$content->id] ?? 0);
                                $requiresReview = ($submission->content->requires_review ?? true) ? true : false;
                                $scoringEnabled = $submission->content->scoring_enabled ?? true;
                                $gradingMode = $submission->content->grading_mode ?? 'individual';

                                if ($totalQuestions === 0) {
                                    $isCompleted = $answers->count() > 0;
                                } elseif (!$requiresReview) {
                                    $isCompleted = $answers->count() > 0;
                                } else {
                                    if (!$scoringEnabled) {
                                        if ($gradingMode === 'overall') {
                                            $isCompleted = $answers->whereNotNull('feedback')->count() > 0;
                                        } else {
                                            $isCompleted = $answers->whereNotNull('feedback')->count() >= $totalQuestions;
                                        }
                                    } else {
                                        if ($gradingMode === 'overall') {
                                            $isCompleted = $answers->whereNotNull('score')->count() > 0;
                                        } else {
                                            $isCompleted = $answers->whereNotNull('score')->count() >= $totalQuestions;
                                        }
                                    }
                                }
                            } else {
                                $isCompleted = false;
                            }
                        } else {
                            $isCompleted = $user->completedContents->contains(function ($c) use ($content) {
                                return $c->id === $content->id && ($c->pivot->completed ?? false);
                            });
                        }

                        if ($isCompleted) {
                            $completed++;
                        }
                    }

                    $percentage = $totalContents > 0 ? round(($completed / $totalContents) * 100, 2) : 0;

                    return [
                        'user' => $user,
                        'progress' => [
                            'progress_percentage' => $percentage,
                            'completed_count' => $completed,
                            'total_count' => $totalContents,
                        ],
                    ];
                });
            }
        }

        return view('admin.force-complete.index', compact('courses', 'selectedCourse', 'participants'));
    }

    /**
     * Force-complete all contents for a single participant in a course
     */
    public function processForceComplete(Request $request)
    {
        $this->authorize('update', Course::class);

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'user_id' => 'required|exists:users,id',
            'generate_certificate' => 'nullable|boolean',
        ]);

        $course = Course::findOrFail($request->course_id);
        $user = User::findOrFail($request->user_id);

        try {
            DB::transaction(function () use ($course, $user) {
                $this->forceCompleteUserInCourse($user, $course);
            });

            // Optionally generate certificate
            if ($request->boolean('generate_certificate')) {
                CertificateController::generateForUser($course, $user);
            }

            return redirect()->back()->with('success', 'Semua konten untuk peserta ' . $user->name . ' telah ditandai selesai.');
        } catch (\Exception $e) {
            Log::error('Force complete error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat force complete: ' . $e->getMessage());
        }
    }

    /**
     * Force-complete all contents for all participants in a course
     */
    public function processForceCompleteAll(Request $request)
    {
        $this->authorize('update', Course::class);

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'generate_certificate' => 'nullable|boolean',
        ]);

        $course = Course::findOrFail($request->course_id);

        try {
            DB::transaction(function () use ($course) {
                foreach ($course->enrolledUsers as $participant) {
                    $this->forceCompleteUserInCourse($participant, $course);
                }
            });

            if ($request->boolean('generate_certificate')) {
                foreach ($course->enrolledUsers as $participant) {
                    CertificateController::generateForUser($course, $participant);
                }
            }

            return redirect()->back()->with('success', 'Semua peserta pada kursus ' . $course->title . ' telah ditandai selesai.');
        } catch (\Exception $e) {
            Log::error('Force complete all error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat force complete massal: ' . $e->getMessage());
        }
    }

    /**
     * Bulk force complete selected participants (with queue)
     */
    public function bulkForceComplete(Request $request)
    {
        $this->authorize('update', Course::class);

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'generate_certificate' => 'nullable|boolean',
            'use_queue' => 'nullable|boolean',
        ]);

        $course = Course::findOrFail($request->course_id);
        $userIds = $request->user_ids;
        $generateCertificate = $request->boolean('generate_certificate');
        $useQueue = $request->boolean('use_queue', true); // Default to queue

        // If more than 50 users or use_queue is true, use job queue
        if ($useQueue || count($userIds) > 50) {
            $batchId = uniqid('bulk_fc_', true);

            // Split into chunks of 50 users per job
            $chunks = array_chunk($userIds, 50);

            foreach ($chunks as $chunk) {
                BulkForceCompleteJob::dispatch($course, $chunk, $generateCertificate, $batchId);
            }

            Log::info("Bulk force complete queued", [
                'batch_id' => $batchId,
                'course_id' => $course->id,
                'total_users' => count($userIds),
                'jobs_created' => count($chunks)
            ]);

            return redirect()->back()->with('success',
                'Proses force complete untuk ' . count($userIds) . ' peserta telah dijadwalkan. ' .
                'Prosesnya akan berjalan di background. Silakan cek log untuk progress. Batch ID: ' . $batchId
            );
        }

        // Process immediately for small batches
        try {
            $processed = 0;
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if ($user) {
                    DB::transaction(function () use ($user, $course) {
                        $this->forceCompleteUserInCourse($user, $course);
                    });

                    if ($generateCertificate) {
                        CertificateController::generateForUser($course, $user);
                    }
                    $processed++;
                }
            }

            return redirect()->back()->with('success',
                'Berhasil force complete ' . $processed . ' peserta dari ' . count($userIds) . ' yang dipilih.'
            );
        } catch (\Exception $e) {
            Log::error('Bulk force complete error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk generate certificates for selected participants (with queue)
     */
    public function bulkGenerateCertificates(Request $request)
    {
        $this->authorize('update', Course::class);

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'use_queue' => 'nullable|boolean',
        ]);

        $course = Course::findOrFail($request->course_id);
        $userIds = $request->user_ids;
        $useQueue = $request->boolean('use_queue', true);

        if (!$course->certificate_template_id) {
            return redirect()->back()->with('error', 'Kursus belum memiliki template sertifikat.');
        }

        // If more than 50 users or use_queue is true, use job queue
        if ($useQueue || count($userIds) > 50) {
            $batchId = uniqid('bulk_cert_', true);

            // Split into chunks of 50 users per job
            $chunks = array_chunk($userIds, 50);

            foreach ($chunks as $chunk) {
                BulkGenerateCertificatesJob::dispatch($course, $chunk, $batchId);
            }

            Log::info("Bulk certificate generation queued", [
                'batch_id' => $batchId,
                'course_id' => $course->id,
                'total_users' => count($userIds),
                'jobs_created' => count($chunks)
            ]);

            return redirect()->back()->with('success',
                'Proses generate sertifikat untuk ' . count($userIds) . ' peserta telah dijadwalkan. ' .
                'Prosesnya akan berjalan di background. Batch ID: ' . $batchId
            );
        }

        // Process immediately for small batches
        try {
            $generated = 0;
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $certificate = CertificateController::generateForUser($course, $user);
                    if ($certificate) {
                        $generated++;
                    }
                }
            }

            return redirect()->back()->with('success',
                'Berhasil generate ' . $generated . ' sertifikat dari ' . count($userIds) . ' peserta yang dipilih.'
            );
        } catch (\Exception $e) {
            Log::error('Bulk certificate generation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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

        // Ensure answers exist (at least one). Create per active question if any.
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
            // Legacy model without questions: create a single placeholder answer if none exists
            if ($submission->answers()->count() === 0) {
                EssayAnswer::create([
                    'submission_id' => $submission->id,
                    'question_id' => null,
                    'answer' => 'Completed offline due to incident',
                ]);
            }
        }

        // Apply grading completion similar to AutoGradeController
        if ($content->requires_review === false) {
            // No review needed: just mark graded_at and status
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

        // Also mark the content as completed in pivot for consistency
        $user->completedContents()->syncWithoutDetaching([
            $content->id => [
                'completed' => true,
                'completed_at' => now(),
            ],
        ]);
    }
}
