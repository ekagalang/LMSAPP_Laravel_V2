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

        return view('gradebook.index', compact('course', 'participants', 'participantsWithEssays', 'allCoursesForFilter'));
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

    private function storeOverallGrade(Request $request, EssaySubmission $submission)
    {
        $validated = $request->validate([
            'overall_score' => 'required|integer|min:0|max:' . $submission->content->essayQuestions()->sum('max_score'),
            'overall_feedback' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $submission) {
            // Update all answers dengan score proporsional dan feedback yang sama
            $questions = $submission->content->essayQuestions()->orderBy('order')->get();
            $totalMaxScore = $questions->sum('max_score');
            $overallScore = $validated['overall_score'];
            
            foreach ($questions as $question) {
                $answer = $submission->answers()->where('question_id', $question->id)->first();
                if ($answer) {
                    // Hitung score proporsional untuk setiap pertanyaan
                    $proportionalScore = $totalMaxScore > 0 
                        ? round(($overallScore * $question->max_score) / $totalMaxScore) 
                        : 0;
                    
                    $answer->update([
                        'score' => $proportionalScore,
                        'feedback' => $validated['overall_feedback']
                    ]);
                }
            }

            // Mark submission as graded
            $submission->update([
                'graded_at' => now(),
                'status' => 'graded'
            ]);
        });

        return redirect()->back()->with('success', 'Penilaian keseluruhan berhasil disimpan.');
    }

    public function showEssayDetail(EssaySubmission $submission)
    {
        $this->authorize('grade', $submission->content->lesson->course);

        // Load relationships
        $submission->load([
            'content.essayQuestions' => function ($query) {
                $query->orderBy('order');
            },
            'answers.question',
            'user'
        ]);

        // Calculate progress variables untuk view
        $totalQuestions = $submission->content->essayQuestions()->count();
        $gradedAnswers = $submission->answers()->whereNotNull('score')->count();

        return view('gradebook.essay_detail', compact('submission', 'totalQuestions', 'gradedAnswers'));
    }
}
