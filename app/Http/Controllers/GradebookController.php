<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\EssaySubmission;
use App\Models\Feedback;
use App\Models\Certificate; // <-- TAMBAHKAN USE STATEMENT
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // <-- TAMBAHKAN USE STATEMENT
use Illuminate\Support\Facades\Storage; // <-- TAMBAHKAN USE STATEMENT
use App\Models\DiscussionReply;
use App\Models\EssayAnswer;
use PDF;

class GradebookController extends Controller
{
    /**
     * Menampilkan halaman Gradebook terpusat dengan filter dan tabs.
     */
    public function index(Request $request, Course $course)
    {
        $activeTab = $request->query('tab', 'essays');
        
        // Redirect scores tab to dedicated quiz scores page
        if ($activeTab === 'scores') {
            return redirect()->route('courses.scores', $course)
                ->with('info', 'Halaman nilai quiz telah dipindahkan. Gunakan menu "Lihat Nilai" di halaman kursus untuk akses cepat.');
        }
        
        $this->authorize('viewGradebook', $course);
        $user = Auth::user();

        $allCoursesForFilter = collect();
        if ($user->can('manage all courses')) {
            $allCoursesForFilter = Course::orderBy('title')->get();
        } else {
            $taught = Course::whereHas('instructors', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->orderBy('title')->get();
            $organized = $user->eventOrganizedCourses()->orderBy('title')->get();
            $allCoursesForFilter = $taught->merge($organized)->unique('id')->values();
        }

        // If user is instructor, only show participants from periods they are assigned to
        if ($user->isInstructorFor($course) && !$user->can('manage all courses') && !$user->isEventOrganizerFor($course)) {
            // Get periods where this instructor is assigned for this course
            $instructorPeriods = $user->instructorPeriods()
                ->where('course_id', $course->id)
                ->pluck('course_classes.id');
            
            if ($instructorPeriods->isNotEmpty()) {
                // Get participants only from instructor's assigned periods
                $participantsQuery = User::whereHas('participantPeriods', function ($query) use ($instructorPeriods) {
                    $query->whereIn('course_classes.id', $instructorPeriods);
                });
            } else {
                // If instructor is not assigned to any periods, show course-level participants
                $participantsQuery = $course->enrolledUsers();
            }
        } else {
            // For super-admin, event-organizer, or other roles, show all participants
            $participantsQuery = $course->enrolledUsers();
        }

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

        // Apply same filtering logic for participants with essays
        if ($user->isInstructorFor($course) && !$user->can('manage all courses') && !$user->isEventOrganizerFor($course)) {
            if ($instructorPeriods->isNotEmpty()) {
                $participantsWithEssaysQuery = User::whereHas('participantPeriods', function ($query) use ($instructorPeriods) {
                    $query->whereIn('course_classes.id', $instructorPeriods);
                })->whereHas('essaySubmissions', fn($q) => $q->whereIn('content_id', $essayContentIds));
            } else {
                $participantsWithEssaysQuery = $course->enrolledUsers()
                    ->whereHas('essaySubmissions', fn($q) => $q->whereIn('content_id', $essayContentIds));
            }
        } else {
            $participantsWithEssaysQuery = $course->enrolledUsers()
                ->whereHas('essaySubmissions', fn($q) => $q->whereIn('content_id', $essayContentIds));
        }

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->input('search');
            $participantsWithEssaysQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }
        $participantsWithEssays = $participantsWithEssaysQuery->get();


        return view('gradebook.index', compact(
            'course',
            'participants',
            'participantsWithEssays',
            'allCoursesForFilter',
            'essayContentIds'
        ));
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
        
        $content = $submission->content;

        if (!$content->scoring_enabled) {
            // Handle feedback only for non-scoring essays
            $feedbacks = $request->input('feedback', []);
            
            DB::transaction(function () use ($feedbacks, $submission, $content) {
                $processedCount = 0;
                
                foreach ($feedbacks as $answerId => $feedback) {
                    $answer = \App\Models\EssayAnswer::where('id', $answerId)
                        ->where('submission_id', $submission->id)
                        ->first();

                    if ($answer && !empty(trim($feedback))) {
                        $answer->update(['feedback' => $feedback]);
                        $processedCount++;
                    }
                }
                
                // Check completion berdasarkan grading mode
                $totalQuestions = $content->essayQuestions()->count();
                $answersWithFeedback = $submission->answers()->whereNotNull('feedback')->count();
                
                $shouldMarkComplete = false;
                if ($content->grading_mode === 'overall') {
                    $shouldMarkComplete = $answersWithFeedback > 0;
                } else {
                    $shouldMarkComplete = $answersWithFeedback >= $totalQuestions;
                }
                
                if ($shouldMarkComplete) {
                    $submission->update([
                        'graded_at' => now(),
                        'status' => 'reviewed'
                    ]);
                }
            });

            return redirect()->back()->with('success', 'Feedback berhasil disimpan.');
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

        $totalMaxScore = $submission->content->essayQuestions()->sum('max_score');
        
        $validated = $request->validate([
            'overall_score' => "required|integer|min:0|max:{$totalMaxScore}",
            'overall_feedback' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $submission) {
                // PERBAIKAN: Update semua answers untuk overall grading
                $allAnswers = $submission->answers;
                
                if ($allAnswers->count() > 0) {
                    // Set score dan feedback ke answer pertama
                    $firstAnswer = $allAnswers->first();
                    $firstAnswer->update([
                        'score' => $validated['overall_score'],
                        'feedback' => $validated['overall_feedback'],
                    ]);
                    
                    // PENTING: Untuk answers lainnya, set score ke 0 dan feedback ke info overall
                    foreach ($allAnswers->skip(1) as $answer) {
                        $answer->update([
                            'score' => 0, // Set ke 0, bukan null
                            'feedback' => 'Dinilai secara keseluruhan. Lihat feedback pada soal pertama.',
                        ]);
                    }
                }

                $submission->update([
                    'graded_at' => now(),
                    'status' => 'graded'
                ]);
            });

            return redirect()->back()->with('success', 'Penilaian keseluruhan berhasil disimpan!');
            
        } catch (\Exception $e) {
            Log::error('Overall grading error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan penilaian.');
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
                $allAnswers = $submission->answers;
                
                if ($allAnswers->count() > 0) {
                    // Set feedback ke answer pertama
                    $firstAnswer = $allAnswers->first();
                    $firstAnswer->update([
                        'feedback' => $validated['overall_feedback'],
                    ]);
                    
                    // Untuk answers lainnya, set feedback ke info overall
                    foreach ($allAnswers->skip(1) as $answer) {
                        $answer->update([
                            'feedback' => 'Dinilai secara keseluruhan. Lihat feedback pada soal pertama.',
                        ]);
                    }
                }

                $submission->update([
                    'graded_at' => now(),
                    'status' => 'reviewed'
                ]);
            });

            return redirect()->back()->with('success', 'Feedback berhasil disimpan!');
            
        } catch (\Exception $e) {
            Log::error('Overall feedback error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan feedback.');
        }
    }

    public function storeEssayFeedbackOnly(Request $request, EssaySubmission $submission)
    {
        $this->authorize('grade', $submission->content->lesson->course);
        
        $content = $submission->content;
        
        if ($content->scoring_enabled) {
            return redirect()->back()->with('error', 'Essay ini memerlukan penilaian dengan scoring.');
        }
        
        $validated = $request->validate([
            'feedback' => 'required|string',
        ]);
        
        try {
            DB::transaction(function () use ($validated, $submission, $content) {
                if ($content->grading_mode === 'overall') {
                    $answer = $submission->answers()->first();
                    if (!$answer) {
                        $answer = $submission->answers()->create([
                            'question_id' => null,
                            'answer' => 'Overall feedback submission',
                            'feedback' => $validated['feedback'],
                        ]);
                    } else {
                        $answer->update(['feedback' => $validated['feedback']]);
                    }
                    
                    $submission->answers()->where('id', '!=', $answer->id)->update(['feedback' => null]);
                } else {
                    foreach ($submission->answers as $answer) {
                        $answer->update(['feedback' => $validated['feedback']]);
                    }
                }
                
                $submission->update([
                    'status' => 'reviewed',
                    'graded_at' => now() // TAMBAH ini untuk marking as completed
                ]);
            });
            
            return redirect()->back()->with('success', 'Feedback berhasil disimpan!');
            
        } catch (\Exception $e) {
            Log::error('Essay feedback error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan feedback.');
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

    /**
     * Instructor Analytics Dashboard - Menampilkan keaktifan semua instruktur
     * ✅ IMPROVED: Sekarang menghitung aktivitas dari course-level DAN period-level
     */
    public function instructorAnalytics(Request $request)
    {
        // Authorization by permissions (EO or higher)
        if (!Auth::user()->can('view instructor analytics') && !Auth::user()->can('view progress reports')) {
            abort(403, 'Unauthorized');
        }

        $dateFrom = $request->get('date_from', now()->subMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        // Get all users who teach at least one course
        $instructors = User::whereHas('instructorCourses')->with(['instructorCourses' => function($query) {
            $query->select('courses.id', 'courses.title');
        }])->get();

        $instructorStats = [];

        foreach ($instructors as $instructor) {
            // Initialize all values to prevent undefined key errors
            $totalDiscussionReplies = 0;
            $totalEssayGraded = 0;
            $totalEssayPending = 0;
            $totalRecentDiscussions = 0;
            $totalRecentGrading = 0;
            $breakdown = [];

            // Get courses yang diajar instructor (dari course_instructor table)
            $instructorCourses = $instructor->instructorCourses;

            // Get periods yang di-assign ke instructor
            $assignedPeriods = $instructor->instructorPeriods()->get();
            $assignedCourseIds = $assignedPeriods->pluck('course_id')->unique();

            foreach ($instructorCourses as $course) {
                // Cek apakah course ini punya periods yang di-assign ke instructor
                $coursePeriods = $assignedPeriods->where('course_id', $course->id);

                if ($coursePeriods->isNotEmpty()) {
                    // ✅ Course ini punya assigned periods - hitung per period
                    foreach ($coursePeriods as $period) {
                        $periodParticipantIds = $period->participants()->pluck('users.id');

                        $periodDiscussionReplies = DiscussionReply::where('user_id', $instructor->id)
                            ->whereBetween('created_at', [$dateFrom, $dateTo])
                            ->whereHas('discussion.content.lesson', function($query) use ($period) {
                                $query->where('course_id', $period->course_id);
                            })
                            ->whereHas('discussion', function($query) use ($periodParticipantIds) {
                                $query->whereIn('user_id', $periodParticipantIds);
                            })->count();

                        $periodEssayGraded = EssayAnswer::whereHas('submission', function($query) use ($periodParticipantIds, $period) {
                                $query->whereIn('user_id', $periodParticipantIds)
                                    ->whereHas('content.lesson', function($subQuery) use ($period) {
                                        $subQuery->where('course_id', $period->course_id);
                                    });
                            })
                            ->where(function($query) {
                                $query->whereNotNull('score')->orWhereNotNull('feedback');
                            })
                            ->whereBetween('updated_at', [$dateFrom, $dateTo])
                            ->count();

                        $periodEssayPending = EssaySubmission::whereIn('user_id', $periodParticipantIds)
                            ->whereHas('content.lesson', function($query) use ($period) {
                                $query->where('course_id', $period->course_id);
                            })
                            ->where(function($query) {
                                $query->whereDoesntHave('answers', function($subQuery) {
                                    $subQuery->whereNotNull('score')->orWhereNotNull('feedback');
                                });
                            })
                            ->count();

                        $periodRecentDiscussions = DiscussionReply::where('user_id', $instructor->id)
                            ->whereBetween('created_at', [now()->subDays(7), now()])
                            ->whereHas('discussion.content.lesson', function($query) use ($period) {
                                $query->where('course_id', $period->course_id);
                            })
                            ->whereHas('discussion', function($query) use ($periodParticipantIds) {
                                $query->whereIn('user_id', $periodParticipantIds);
                            })->count();

                        $periodRecentGrading = EssayAnswer::whereHas('submission', function($query) use ($periodParticipantIds, $period) {
                                $query->whereIn('user_id', $periodParticipantIds)
                                    ->whereHas('content.lesson', function($subQuery) use ($period) {
                                        $subQuery->where('course_id', $period->course_id);
                                    });
                            })
                            ->where(function($query) {
                                $query->whereNotNull('score')->orWhereNotNull('feedback');
                            })
                            ->whereBetween('updated_at', [now()->subDays(7), now()])
                            ->count();

                        $totalDiscussionReplies += $periodDiscussionReplies;
                        $totalEssayGraded += $periodEssayGraded;
                        $totalEssayPending += $periodEssayPending;
                        $totalRecentDiscussions += $periodRecentDiscussions;
                        $totalRecentGrading += $periodRecentGrading;

                        $breakdown[] = [
                            'type' => 'period',
                            'period' => $period,
                            'course' => $course,
                            'participants_count' => $period->participants()->count(),
                            'discussion_replies' => $periodDiscussionReplies,
                            'essay_graded' => $periodEssayGraded,
                            'essay_pending' => $periodEssayPending,
                            'recent_discussions' => $periodRecentDiscussions,
                            'recent_grading' => $periodRecentGrading,
                            'activity_score' => $periodDiscussionReplies + $periodEssayGraded,
                        ];
                    }
                } else {
                    // ✅ Course ini TIDAK punya assigned periods - hitung dari SEMUA participants di course
                    $courseParticipantIds = $course->enrolledUsers()->pluck('users.id');

                    $courseDiscussionReplies = DiscussionReply::where('user_id', $instructor->id)
                        ->whereBetween('created_at', [$dateFrom, $dateTo])
                        ->whereHas('discussion.content.lesson', function($query) use ($course) {
                            $query->where('course_id', $course->id);
                        })
                        ->whereHas('discussion', function($query) use ($courseParticipantIds) {
                            $query->whereIn('user_id', $courseParticipantIds);
                        })->count();

                    $courseEssayGraded = EssayAnswer::whereHas('submission', function($query) use ($courseParticipantIds, $course) {
                            $query->whereIn('user_id', $courseParticipantIds)
                                ->whereHas('content.lesson', function($subQuery) use ($course) {
                                    $subQuery->where('course_id', $course->id);
                                });
                        })
                        ->where(function($query) {
                            $query->whereNotNull('score')->orWhereNotNull('feedback');
                        })
                        ->whereBetween('updated_at', [$dateFrom, $dateTo])
                        ->count();

                    $courseEssayPending = EssaySubmission::whereIn('user_id', $courseParticipantIds)
                        ->whereHas('content.lesson', function($query) use ($course) {
                            $query->where('course_id', $course->id);
                        })
                        ->where(function($query) {
                            $query->whereDoesntHave('answers', function($subQuery) {
                                $subQuery->whereNotNull('score')->orWhereNotNull('feedback');
                            });
                        })
                        ->count();

                    $courseRecentDiscussions = DiscussionReply::where('user_id', $instructor->id)
                        ->whereBetween('created_at', [now()->subDays(7), now()])
                        ->whereHas('discussion.content.lesson', function($query) use ($course) {
                            $query->where('course_id', $course->id);
                        })
                        ->whereHas('discussion', function($query) use ($courseParticipantIds) {
                            $query->whereIn('user_id', $courseParticipantIds);
                        })->count();

                    $courseRecentGrading = EssayAnswer::whereHas('submission', function($query) use ($courseParticipantIds, $course) {
                            $query->whereIn('user_id', $courseParticipantIds)
                                ->whereHas('content.lesson', function($subQuery) use ($course) {
                                    $subQuery->where('course_id', $course->id);
                                });
                        })
                        ->where(function($query) {
                            $query->whereNotNull('score')->orWhereNotNull('feedback');
                        })
                        ->whereBetween('updated_at', [now()->subDays(7), now()])
                        ->count();

                    $totalDiscussionReplies += $courseDiscussionReplies;
                    $totalEssayGraded += $courseEssayGraded;
                    $totalEssayPending += $courseEssayPending;
                    $totalRecentDiscussions += $courseRecentDiscussions;
                    $totalRecentGrading += $courseRecentGrading;

                    $breakdown[] = [
                        'type' => 'course',
                        'period' => null,
                        'course' => $course,
                        'participants_count' => $courseParticipantIds->count(),
                        'discussion_replies' => $courseDiscussionReplies,
                        'essay_graded' => $courseEssayGraded,
                        'essay_pending' => $courseEssayPending,
                        'recent_discussions' => $courseRecentDiscussions,
                        'recent_grading' => $courseRecentGrading,
                        'activity_score' => $courseDiscussionReplies + $courseEssayGraded,
                    ];
                }
            }

            $instructorStats[] = [
                'instructor' => $instructor,
                'courses' => $instructorCourses,
                'periods' => $assignedPeriods,
                'breakdown' => $breakdown,
                'discussion_replies' => $totalDiscussionReplies,
                'essay_graded' => $totalEssayGraded,
                'essay_pending' => $totalEssayPending,
                'recent_discussions' => $totalRecentDiscussions,
                'recent_grading' => $totalRecentGrading,
                'total_activity' => $totalDiscussionReplies + $totalEssayGraded,
                'recent_activity' => $totalRecentDiscussions + $totalRecentGrading,
            ];
        }

        // Sort by total activity
        usort($instructorStats, function($a, $b) {
            return $b['total_activity'] <=> $a['total_activity'];
        });

        // Overall statistics
        $overallStats = [
            'total_instructors' => $instructors->count(),
            'active_instructors' => collect($instructorStats)->where('recent_activity', '>', 0)->count(),
            'total_discussion_replies' => collect($instructorStats)->sum('discussion_replies'),
            'total_essays_graded' => collect($instructorStats)->sum('essay_graded'),
            'total_essays_pending' => collect($instructorStats)->sum('essay_pending'),
        ];

        return view('instructor-analytics.index', compact(
            'instructorStats',
            'overallStats',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Detail analytics untuk instruktur tertentu
     * ✅ IMPROVED: Sekarang include course-level data juga
     */
    public function instructorDetail(Request $request, User $user)
    {
        // Authorization by permissions (EO or higher)
        if (!Auth::user()->can('view instructor analytics') && !Auth::user()->can('view progress reports')) {
            abort(403, 'Unauthorized');
        }

        // Pastikan user punya course yang diajar
        if (!$user->instructorCourses()->exists()) {
            abort(404, 'Instructor not found');
        }

        $dateFrom = $request->get('date_from', now()->subMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        $courseId = $request->get('course_id');

        // ✅ Get ALL courses taught by instructor (from course_instructor table)
        $courses = $user->instructorCourses;
        if ($courseId) {
            $courses = $courses->where('id', $courseId);
        }
        $courseIds = $courses->pluck('id');

        // Get periods assigned to this instructor
        $assignedPeriods = $user->instructorPeriods()->get();
        if ($courseId) {
            $assignedPeriods = $assignedPeriods->where('course_id', $courseId);
        }

        // ✅ Get all participant IDs from BOTH periods AND course-level enrollments
        $allParticipantIds = collect();

        foreach ($courses as $course) {
            $coursePeriods = $assignedPeriods->where('course_id', $course->id);

            if ($coursePeriods->isNotEmpty()) {
                // Ada assigned periods - ambil participants dari periods
                foreach ($coursePeriods as $period) {
                    $allParticipantIds = $allParticipantIds->merge($period->participants()->pluck('users.id'));
                }
            } else {
                // Tidak ada assigned periods - ambil SEMUA participants di course
                $allParticipantIds = $allParticipantIds->merge($course->enrolledUsers()->pluck('users.id'));
            }
        }
        $allParticipantIds = $allParticipantIds->unique();

        // Discussion activity with details - only from assigned period participants
        $discussionReplies = DiscussionReply::where('user_id', $user->id)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereHas('discussion.content.lesson', function($query) use ($courseIds) {
                $query->whereIn('course_id', $courseIds);
            })
            ->whereHas('discussion', function($query) use ($allParticipantIds) {
                $query->whereIn('user_id', $allParticipantIds);
            })
            ->with(['discussion.content.lesson.course', 'discussion.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Essay grading activity with details - only from assigned period participants
        $essayGrading = EssayAnswer::whereHas('submission', function($query) use ($allParticipantIds, $courseIds) {
                $query->whereIn('user_id', $allParticipantIds)
                    ->whereHas('content.lesson', function($subQuery) use ($courseIds) {
                        $subQuery->whereIn('course_id', $courseIds);
                    });
            })
            ->where(function($query) {
                $query->whereNotNull('score')->orWhereNotNull('feedback');
            })
            ->whereBetween('updated_at', [$dateFrom, $dateTo])
            ->with(['submission.user', 'submission.content.lesson.course', 'question'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Monthly statistics
        $monthlyStats = [];
        $currentDate = now()->parse($dateFrom);
        $endDate = now()->parse($dateTo);

        while ($currentDate->lte($endDate)) {
            $month = $currentDate->format('Y-m');
            $monthStart = $currentDate->startOfMonth()->toDateString();
            $monthEnd = $currentDate->endOfMonth()->toDateString();

            $discussions = DiscussionReply::where('user_id', $user->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereHas('discussion.content.lesson', function($query) use ($courseIds) {
                    $query->whereIn('course_id', $courseIds);
                })
                ->whereHas('discussion', function($query) use ($allParticipantIds) {
                    $query->whereIn('user_id', $allParticipantIds);
                })->count();

            $grading = EssayAnswer::whereHas('submission', function($query) use ($allParticipantIds, $courseIds) {
                    $query->whereIn('user_id', $allParticipantIds)
                        ->whereHas('content.lesson', function($subQuery) use ($courseIds) {
                            $subQuery->whereIn('course_id', $courseIds);
                        });
                })
                ->where(function($query) {
                    $query->whereNotNull('score')->orWhereNotNull('feedback');
                })
                ->whereBetween('updated_at', [$monthStart, $monthEnd])
                ->count();

            $monthlyStats[] = [
                'month' => $currentDate->format('M Y'),
                'discussions' => $discussions,
                'grading' => $grading,
                'total' => $discussions + $grading
            ];

            $currentDate->addMonth();
        }

        // ✅ IMPROVED: Statistics per course/period (include course-level data)
        $periodStats = [];

        foreach ($courses as $course) {
            $coursePeriods = $assignedPeriods->where('course_id', $course->id);

            if ($coursePeriods->isNotEmpty()) {
                // Ada assigned periods - tampilkan per period
                foreach ($coursePeriods as $period) {
                    $periodParticipantIds = $period->participants()->pluck('users.id');

                    $discussions = DiscussionReply::where('user_id', $user->id)
                        ->whereBetween('created_at', [$dateFrom, $dateTo])
                        ->whereHas('discussion.content.lesson', function($query) use ($period) {
                            $query->where('course_id', $period->course_id);
                        })
                        ->whereHas('discussion', function($query) use ($periodParticipantIds) {
                            $query->whereIn('user_id', $periodParticipantIds);
                        })->count();

                    $grading = EssayAnswer::whereHas('submission', function($query) use ($periodParticipantIds, $period) {
                            $query->whereIn('user_id', $periodParticipantIds)
                                ->whereHas('content.lesson', function($subQuery) use ($period) {
                                    $subQuery->where('course_id', $period->course_id);
                                });
                        })
                        ->where(function($query) {
                            $query->whereNotNull('score')->orWhereNotNull('feedback');
                        })
                        ->whereBetween('updated_at', [$dateFrom, $dateTo])
                        ->count();

                    $periodStats[] = [
                        'type' => 'period',
                        'period' => $period,
                        'course' => $period->course,
                        'participants_count' => $period->participants()->count(),
                        'discussions' => $discussions,
                        'grading' => $grading,
                        'total' => $discussions + $grading
                    ];
                }
            } else {
                // Tidak ada assigned periods - tampilkan course-level
                $courseParticipantIds = $course->enrolledUsers()->pluck('users.id');

                $discussions = DiscussionReply::where('user_id', $user->id)
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->whereHas('discussion.content.lesson', function($query) use ($course) {
                        $query->where('course_id', $course->id);
                    })
                    ->whereHas('discussion', function($query) use ($courseParticipantIds) {
                        $query->whereIn('user_id', $courseParticipantIds);
                    })->count();

                $grading = EssayAnswer::whereHas('submission', function($query) use ($courseParticipantIds, $course) {
                        $query->whereIn('user_id', $courseParticipantIds)
                            ->whereHas('content.lesson', function($subQuery) use ($course) {
                                $subQuery->where('course_id', $course->id);
                            });
                    })
                    ->where(function($query) {
                        $query->whereNotNull('score')->orWhereNotNull('feedback');
                    })
                    ->whereBetween('updated_at', [$dateFrom, $dateTo])
                    ->count();

                $periodStats[] = [
                    'type' => 'course',
                    'period' => null,
                    'course' => $course,
                    'participants_count' => $courseParticipantIds->count(),
                    'discussions' => $discussions,
                    'grading' => $grading,
                    'total' => $discussions + $grading
                ];
            }
        }

        $stats = [
            'discussion_replies' => $discussionReplies->count(),
            'essay_graded' => $essayGrading->count(),
            'total_activity' => $discussionReplies->count() + $essayGrading->count(),
        ];

        return view('instructor-analytics.detail', compact(
            'user',
            'courses',
            'assignedPeriods',
            'discussionReplies',
            'essayGrading',
            'monthlyStats',
            'periodStats',
            'stats',
            'dateFrom',
            'dateTo',
            'courseId'
        ));
    }

    /**
     * Compare multiple instructors
     */
    public function instructorCompare(Request $request)
    {
        // Authorization by permissions (EO or higher)
        if (!Auth::user()->can('view instructor analytics') && !Auth::user()->can('view progress reports')) {
            abort(403, 'Unauthorized');
        }

        $instructorIds = $request->get('instructors', []);
        $dateFrom = $request->get('date_from', now()->subMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        $allInstructors = User::whereHas('instructorCourses')->orderBy('name')->get();
        
        if (empty($instructorIds)) {
            return view('instructor-analytics.compare', compact('allInstructors', 'dateFrom', 'dateTo'));
        }

        $compareData = [];
        foreach ($instructorIds as $instructorId) {
            $instructor = User::find($instructorId);
            if (!$instructor || !$instructor->instructorCourses()->exists()) continue;

            $courseIds = $instructor->instructorCourses->pluck('id');

            $discussions = DiscussionReply::where('user_id', $instructor->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->whereHas('discussion.content.lesson', function($query) use ($courseIds) {
                    $query->whereIn('course_id', $courseIds);
                })->count();

            $grading = EssayAnswer::whereHas('submission.content.lesson', function($query) use ($courseIds) {
                    $query->whereIn('course_id', $courseIds);
                })
                ->where(function($query) {
                    $query->whereNotNull('score')->orWhereNotNull('feedback');
                })
                ->whereBetween('updated_at', [$dateFrom, $dateTo])
                ->count();

            $compareData[] = [
                'instructor' => $instructor,
                'discussions' => $discussions,
                'grading' => $grading,
                'total' => $discussions + $grading,
                'courses_count' => $instructor->instructorCourses->count(),
            ];
        }

        return view('instructor-analytics.compare', compact(
            'allInstructors',
            'compareData',
            'instructorIds',
            'dateFrom',
            'dateTo'
        ));
    }
}
