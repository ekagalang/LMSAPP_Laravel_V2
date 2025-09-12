<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\EssaySubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoGradeController extends Controller
{
    /**
     * Show the automatic grading completion interface
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Course::class);

        $courses = Course::with(['enrolledUsers', 'lessons.contents'])->orderBy('title')->get();
        $selectedCourse = null;
        $participants = collect();
        
        // If a course is selected, get the participants
        if ($request->has('course_id') && $request->course_id) {
            $selectedCourse = Course::find($request->course_id);
            if ($selectedCourse) {
                $participants = $this->getParticipantsWithSubmissions($selectedCourse);
            }
        }
        
        return view('admin.auto-grade.index', compact('courses', 'selectedCourse', 'participants'));
    }

    /**
     * Get participants with essay submissions for a course
     */
    private function getParticipantsWithSubmissions(Course $course)
    {
        try {
            // Get all essay content IDs for this course
            $essayContentIds = $course->lessons()->with('contents')
                ->get()
                ->pluck('contents')
                ->flatten()
                ->where('type', 'essay')
                ->pluck('id');

            if ($essayContentIds->isEmpty()) {
                Log::info("No essay contents found for course: " . $course->title);
                return collect();
            }

            // Get all participants for this course who have essay submissions
            $participants = $course->enrolledUsers()
                ->whereHas('essaySubmissions', function($query) use ($essayContentIds) {
                    $query->whereIn('content_id', $essayContentIds);
                })
                ->with(['essaySubmissions' => function($query) use ($essayContentIds) {
                    // Only load submissions for this course
                    $query->whereIn('content_id', $essayContentIds)
                          ->with('content');
                }])
                ->get();

            // Process participants
            $processedParticipants = collect();
            
            foreach ($participants as $participant) {
                // Get submissions for this course only
                $allSubmissionsForCourse = $participant->essaySubmissions;
                $pendingSubmissions = $this->getPendingSubmissionsForParticipant($participant, $course);
                
                // Get progress information
                $progress = $participant->getProgressForCourse($course);
                
                $processedParticipants->push([
                    'user' => $participant,
                    'pending_submissions' => $pendingSubmissions,
                    'all_submissions' => $allSubmissionsForCourse, // Now only contains submissions for this course
                    'progress' => $progress,
                    'total_contents' => $progress['total_count'] ?? 0,
                    'completed_contents' => $progress['completed_count'] ?? 0,
                    'progress_percentage' => $progress['progress_percentage'] ?? 0
                ]);
            }

            return $processedParticipants;
        } catch (\Exception $e) {
            Log::error('Error in getParticipantsWithSubmissions: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return collect(); // Return empty collection on error
        }
    }

    /**
     * Get pending essay submissions for a participant in a course
     */
    private function getPendingSubmissionsForParticipant(User $participant, Course $course)
    {
        try {
            // Get all essay content IDs for this course
            $essayContentIds = $course->lessons()->with('contents')
                ->get()
                ->pluck('contents')
                ->flatten()
                ->where('type', 'essay')
                ->pluck('id');

            // Get ALL essay submissions for this participant in this course
            $submissions = EssaySubmission::with(['content', 'answers'])
                ->where('user_id', $participant->id)
                ->whereIn('content_id', $essayContentIds)
                ->get();

            // Filter submissions that need grading
            $pendingSubmissions = $submissions->filter(function ($submission) {
                // A submission needs grading if:
                // 1. It has answers (participant has submitted something)
                // 2. It's not fully graded yet
                // 3. It requires review (not a self-completed exercise)
                return $submission->answers && $submission->answers->count() > 0 && 
                       !$submission->is_fully_graded && 
                       ($submission->content->requires_review ?? true);
            });

            return $pendingSubmissions;
        } catch (\Exception $e) {
            Log::error('Error in getPendingSubmissionsForParticipant: ' . $e->getMessage());
            return collect(); // Return empty collection on error
        }
    }

    /**
     * Process auto grading for a participant
     */
    public function processAutoGrade(Request $request)
    {
        $this->authorize('update', Course::class);

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $course = Course::find($request->course_id);
        $user = User::find($request->user_id);

        try {
            DB::transaction(function () use ($user, $course) {
                // Get all essay content IDs for this course
                $essayContentIds = $course->lessons()->with('contents')
                    ->get()
                    ->pluck('contents')
                    ->flatten()
                    ->where('type', 'essay')
                    ->pluck('id');

                // Get submissions that need grading
                $submissions = EssaySubmission::with(['content', 'answers'])
                    ->where('user_id', $user->id)
                    ->whereIn('content_id', $essayContentIds)
                    ->get()
                    ->filter(function ($submission) {
                        // Filter submissions that are not yet fully graded
                        return !$submission->is_fully_graded;
                    });

                // Process each pending submission
                foreach ($submissions as $submission) {
                    $this->processSubmission($submission);
                }
            });

            return redirect()->back()->with('success', 'Penilaian otomatis berhasil diselesaikan untuk peserta ' . $user->name);
        } catch (\Exception $e) {
            Log::error('Auto grading error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat melakukan penilaian otomatis: ' . $e->getMessage());
        }
    }

    /**
     * Process a single submission to mark it as completed without scores
     */
    private function processSubmission(EssaySubmission $submission)
    {
        $content = $submission->content;
        
        // If already processed, skip
        if ($submission->is_fully_graded) {
            return;
        }

        // If essay doesn't require review, it's already complete
        if (!($content->requires_review ?? true)) {
            return;
        }

        // If scoring is enabled, we need to add a zero score
        if ($content->scoring_enabled) {
            // For overall grading, add score to first answer
            if ($content->grading_mode === 'overall') {
                $firstAnswer = $submission->answers()->first();
                if ($firstAnswer) {
                    $firstAnswer->update([
                        'score' => 0,
                        'feedback' => $firstAnswer->feedback ?? 'Dinilai secara otomatis karena instruktur tidak menyelesaikan penilaian'
                    ]);
                }
            } else {
                // For individual grading, add zero scores to all answers without scores
                foreach ($submission->answers as $answer) {
                    if ($answer->score === null) {
                        $answer->update([
                            'score' => 0,
                            'feedback' => $answer->feedback ?? 'Dinilai secara otomatis karena instruktur tidak menyelesaikan penilaian'
                        ]);
                    }
                }
            }
        } else {
            // For non-scoring essays, just add feedback if missing
            if ($content->grading_mode === 'overall') {
                $firstAnswer = $submission->answers()->first();
                if ($firstAnswer && empty($firstAnswer->feedback)) {
                    $firstAnswer->update([
                        'feedback' => 'Dinilai secara otomatis karena instruktur tidak menyelesaikan penilaian'
                    ]);
                }
            } else {
                // For individual grading, add feedback to all answers without feedback
                foreach ($submission->answers as $answer) {
                    if (empty($answer->feedback)) {
                        $answer->update([
                            'feedback' => 'Dinilai secara otomatis karena instruktur tidak menyelesaikan penilaian'
                        ]);
                    }
                }
            }
        }

        // Mark submission as graded
        $submission->update([
            'graded_at' => now(),
            'status' => $content->scoring_enabled ? 'graded' : 'reviewed'
        ]);
    }

    /**
     * Process auto grading for all participants in a course
     */
    public function processAutoGradeAll(Request $request)
    {
        $this->authorize('update', Course::class);

        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $course = Course::find($request->course_id);

        try {
            DB::transaction(function () use ($course) {
                // Get all participants for this course
                $participants = $course->enrolledUsers;

                foreach ($participants as $participant) {
                    // Get all essay content IDs for this course
                    $essayContentIds = $course->lessons()->with('contents')
                        ->get()
                        ->pluck('contents')
                        ->flatten()
                        ->where('type', 'essay')
                        ->pluck('id');

                    // Get submissions that need grading
                    $submissions = EssaySubmission::with(['content', 'answers'])
                        ->where('user_id', $participant->id)
                        ->whereIn('content_id', $essayContentIds)
                        ->get()
                        ->filter(function ($submission) {
                            // Filter submissions that are not yet fully graded
                            return !$submission->is_fully_graded;
                        });

                    // Process each pending submission
                    foreach ($submissions as $submission) {
                        $this->processSubmission($submission);
                    }
                }
            });

            return redirect()->back()->with('success', 'Penilaian otomatis berhasil diselesaikan untuk semua peserta dalam kursus ' . $course->title);
        } catch (\Exception $e) {
            Log::error('Auto grading all error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat melakukan penilaian otomatis untuk semua peserta: ' . $e->getMessage());
        }
    }
}