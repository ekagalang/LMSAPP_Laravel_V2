<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\EssaySubmission;
use App\Models\Feedback;
use App\Models\Certificate; // <-- TAMBAHKAN USE STATEMENT
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // <-- TAMBAHKAN USE STATEMENT
use Illuminate\Support\Facades\Storage; // <-- TAMBAHKAN USE STATEMENT
use PDF;

class GradebookController extends Controller
{
    /**
     * Menampilkan halaman Gradebook terpusat dengan filter dan tabs.
     */
    public function index(Request $request, Course $course)
    {
        $this->authorize('viewGradebook', $course);
        $user = Auth::user();

        $allCoursesForFilter = collect();
        if ($user->hasRole('super-admin')) {
            $allCoursesForFilter = Course::orderBy('title')->get();
        } elseif ($user->hasRole('instructor')) {
            $allCoursesForFilter = Course::whereHas('instructors', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->orderBy('title')->get();
        }

        $participantsQuery = $course->enrolledUsers();

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $participantsQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }
        $participants = $participantsQuery->with(['feedback' => function ($query) use ($course) {
            $query->where('course_id', $course->id);
        }])->get();

        $essayContentIds = $course->lessons()->with('contents')
            ->get()->pluck('contents')->flatten()->where('type', 'essay')->pluck('id');

        $participantsWithEssaysQuery = $course->enrolledUsers()
            ->whereHas('essaySubmissions', fn($q) => $q->whereIn('content_id', $essayContentIds));

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->input('search');
            $participantsWithEssaysQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }
        $participantsWithEssays = $participantsWithEssaysQuery->get();

        return view('gradebook.index', compact('course', 'participants', 'participantsWithEssays', 'allCoursesForFilter', 'essayContentIds'));
    }

    /**
     * Menampilkan semua jawaban esai dari satu peserta.
     */
    public function showUserEssays(Course $course, User $user)
    {
        $this->authorize('grade', $course);

        $essayContentIds = $course->lessons()->with('contents')
            ->get()->pluck('contents')->flatten()->where('type', 'essay')->pluck('id');

        $submissions = EssaySubmission::with('content')
            ->where('user_id', $user->id)
            ->whereIn('content_id', $essayContentIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('gradebook.user_essays', compact('course', 'user', 'submissions'));
    }

    /**
     * Menyimpan nilai dan feedback untuk sebuah jawaban esai.
     */
    public function storeEssayGrade(Request $request, EssaySubmission $submission)
    {
        $this->authorize('grade', $submission->content->lesson->course);

        // Cek apakah essay memerlukan scoring
        if (!$submission->content->scoring_enabled) {
            // Untuk essay tanpa scoring, hanya simpan feedback
            $validated = $request->validate([
                'feedback' => 'nullable|string',
            ]);

            $answer = $submission->answers()->first();
            if ($answer) {
                $answer->update(['feedback' => $validated['feedback']]);
            }

            return redirect()->route('gradebook.user_essays', [
                'course' => $submission->content->lesson->course->id,
                'user' => $submission->user_id
            ])->with('success', 'Catatan untuk ' . $submission->user->name . ' berhasil disimpan.');
        }

        // Untuk essay dengan scoring
        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $updateData = $validated + ['graded_at' => now()];

        try {
            $updateData['status'] = 'graded';
            $submission->update($updateData);
        } catch (\Exception $e) {
            unset($updateData['status']);
            $submission->update($updateData);
        }

        return redirect()->route('gradebook.user_essays', [
            'course' => $submission->content->lesson->course->id,
            'user' => $submission->user_id
        ])->with('success', 'Nilai untuk ' . $submission->user->name . ' berhasil disimpan.');
    }

    /**
     * Menyimpan feedback umum untuk seorang peserta.
     */
    public function storeFeedback(Request $request, Course $course, User $user)
    {
        $this->authorize('grade', $course);

        $request->validate(['feedback' => 'required|string']);

        Feedback::updateOrCreate(
            ['course_id' => $course->id, 'user_id' => $user->id],
            ['feedback' => $request->feedback, 'instructor_id' => auth()->id()]
        );

        return redirect()->back()->with('success', 'Feedback untuk ' . $user->name . ' berhasil disimpan.');
    }

    public function storeMultiQuestionGrade(Request $request, EssaySubmission $submission)
    {
        $this->authorize('grade', $submission->content->lesson->course);

        if (!$submission->content->scoring_enabled) {
            // Handle feedback only for non-scoring essays
            $feedbacks = $request->input('feedback', []);
            
            DB::transaction(function () use ($feedbacks, $submission) {
                foreach ($feedbacks as $answerId => $feedback) {
                    $answer = \App\Models\EssayAnswer::where('id', $answerId)
                        ->where('submission_id', $submission->id)
                        ->first();

                    if ($answer) {
                        $answer->update(['feedback' => $feedback]);
                    }
                }
            });

            return redirect()->back()->with('success', 'Catatan berhasil disimpan.');
        }

        // Handle scoring essays based on grading mode
        if ($submission->content->grading_mode === 'overall') {
            return $this->storeOverallGrade($request, $submission);
        } else {
            return $this->storeIndividualGrades($request, $submission);
        }
    }

    private function storeIndividualGrades(Request $request, EssaySubmission $submission)
    {
        $scores = $request->input('scores', []);
        $feedbacks = $request->input('feedback', []);
        $gradedCount = 0;

        DB::transaction(function () use ($scores, $feedbacks, $submission, &$gradedCount) {
            foreach ($feedbacks as $answerId => $feedback) {
                $answer = \App\Models\EssayAnswer::where('id', $answerId)
                    ->where('submission_id', $submission->id)
                    ->first();

                if ($answer) {
                    $updateData = ['feedback' => $feedback];
                    
                    if (isset($scores[$answerId]) && $scores[$answerId] !== null && $scores[$answerId] !== '') {
                        $updateData['score'] = (int) $scores[$answerId];
                        $gradedCount++;
                    }

                    $answer->update($updateData);
                }
            }

            // Update submission graded_at jika semua questions sudah graded
            $totalQuestions = $submission->content->essayQuestions()->count();
            $currentGradedAnswers = $submission->answers()->whereNotNull('score')->count();

            if ($currentGradedAnswers >= $totalQuestions) {
                $submission->update([
                    'graded_at' => now(),
                    'status' => 'graded'
                ]);
            }
        });

        $message = "Berhasil menyimpan {$gradedCount} nilai individual.";
        return redirect()->back()->with('success', $message);
    }

    public function storeOverallGrade(Request $request, EssaySubmission $submission)
    {
        $this->authorize('grade', $submission->content->lesson->course);

        // Validate input
        $totalMaxScore = $submission->content->essayQuestions()->sum('max_score');
        
        $validated = $request->validate([
            'overall_score' => "required|integer|min:0|max:{$totalMaxScore}",
            'overall_feedback' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $submission) {
                // Get first answer or create if doesn't exist
                $firstAnswer = $submission->answers()->first();
                
                if (!$firstAnswer) {
                    // Create a dummy answer for overall grading
                    $firstQuestion = $submission->content->essayQuestions()->orderBy('order')->first();
                    if ($firstQuestion) {
                        $firstAnswer = $submission->answers()->create([
                            'question_id' => $firstQuestion->id,
                            'answer' => 'Overall grading - see individual answers above',
                        ]);
                    }
                }
                
                if ($firstAnswer) {
                    // Store overall score and feedback in the first answer
                    $firstAnswer->update([
                        'score' => $validated['overall_score'],
                        'feedback' => $validated['overall_feedback'],
                    ]);
                    
                    // Clear scores from other answers to avoid confusion
                    $submission->answers()->where('id', '!=', $firstAnswer->id)->update([
                        'score' => null,
                        'feedback' => null
                    ]);
                }

                // Update submission status
                $submission->update([
                    'graded_at' => now(),
                    'status' => 'graded'
                ]);
            });

            return redirect()->back()->with('success', 'Penilaian keseluruhan berhasil disimpan!');
            
        } catch (\Exception $e) {
            Log::error('Overall grading error: ' . $e->getMessage(), [
                'submission_id' => $submission->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan penilaian. Silakan coba lagi.');
        }
    }

    public function storeOverallFeedback(Request $request, EssaySubmission $submission)
    {
        $this->authorize('grade', $submission->content->lesson->course);

        $validated = $request->validate([
            'overall_feedback' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $submission) {
                // Get first answer or create if doesn't exist
                $firstAnswer = $submission->answers()->first();
                
                if (!$firstAnswer) {
                    // Create a dummy answer for overall feedback
                    $firstQuestion = $submission->content->essayQuestions()->orderBy('order')->first();
                    if ($firstQuestion) {
                        $firstAnswer = $submission->answers()->create([
                            'question_id' => $firstQuestion->id,
                            'answer' => 'Overall feedback - see individual answers above',
                        ]);
                    } else {
                        // Legacy essay without questions
                        $firstAnswer = $submission->answers()->create([
                            'question_id' => null,
                            'answer' => 'Overall feedback for essay',
                        ]);
                    }
                }
                
                if ($firstAnswer) {
                    // Store overall feedback in the first answer, clear others
                    $firstAnswer->update([
                        'feedback' => $validated['overall_feedback'],
                    ]);
                    
                    // Clear feedback from other answers to avoid confusion
                    $submission->answers()->where('id', '!=', $firstAnswer->id)->update([
                        'feedback' => null
                    ]);
                }

                // Update submission status (no graded_at for feedback-only)
                $submission->update([
                    'status' => 'reviewed'
                ]);
            });

            return redirect()->back()->with('success', 'Feedback berhasil disimpan!');
            
        } catch (\Exception $e) {
            Log::error('Overall feedback error: ' . $e->getMessage(), [
                'submission_id' => $submission->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan feedback. Silakan coba lagi.');
        }
    }

    public function showEssayDetail(EssaySubmission $submission)
    {
        $this->authorize('grade', $submission->content->lesson->course);

        $submission->load([
            'content.essayQuestions' => function ($query) {
                $query->orderBy('order');
            },
            'answers.question',
            'user'
        ]);

        $gradingMode = $submission->content->grading_mode ?? 'individual';
        $scoringEnabled = $submission->content->scoring_enabled ?? true;
        
        return view('gradebook.essay_detail', compact('submission', 'gradingMode', 'scoringEnabled'));
    }
}
