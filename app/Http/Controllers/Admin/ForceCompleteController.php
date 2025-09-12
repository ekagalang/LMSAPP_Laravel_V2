<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CertificateController;
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

        $courses = Course::with(['enrolledUsers', 'lessons.contents'])->orderBy('title')->get();
        $selectedCourse = null;
        $participants = collect();

        if ($request->has('course_id') && $request->course_id) {
            $selectedCourse = Course::find($request->course_id);
            if ($selectedCourse) {
                $participants = $selectedCourse->enrolledUsers
                    ->map(function ($user) use ($selectedCourse) {
                        $progress = $user->getProgressForCourse($selectedCourse);
                        return [
                            'user' => $user,
                            'progress' => $progress,
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

        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'score' => max($quiz->pass_marks, 0),
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

