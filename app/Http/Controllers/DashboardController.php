<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Course;
use App\Models\Discussion;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\EssaySubmission;
use App\Models\Feedback;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('super-admin')) {
            $stats = $this->getAdminStats();
            return view('dashboard.admin', compact('stats'));
        }

        if ($user->hasRole('instructor')) {
            $stats = $this->getInstructorStats($user);
            return view('dashboard.instructor', compact('stats'));
        }

        if ($user->hasRole('event-organizer')) {
            $stats = $this->getEoStats($user);
            return view('dashboard.eo', compact('stats'));
        }

        // For Participant
        $enrolledCourses = $user->enrolledCourses()->with('instructor')->where('status', 'published')->get();
        return view('dashboard.participant', compact('enrolledCourses'));
    }

    private function getAdminStats()
    {
        // Total users by role
        $totalUsers = User::count();
        $totalParticipants = User::role('participant')->count();
        $totalInstructors = User::role('instructor')->count();
        $totalEventOrganizers = User::role('event-organizer')->count();

        // Course statistics
        $totalCourses = Course::count();
        $publishedCourses = Course::where('status', 'published')->count();
        $draftCourses = Course::where('status', 'draft')->count();

        // Recent enrollments (last 30 days)
        $recentEnrollments = DB::table('course_user')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // Discussion statistics
        $totalDiscussions = Discussion::count();
        $recentDiscussions = Discussion::where('created_at', '>=', now()->subDays(7))->count();

        // Quiz statistics
        $totalQuizzes = Quiz::count();
        $quizAttempts = QuizAttempt::count();
        $completedQuizzes = QuizAttempt::whereNotNull('completed_at')->count();

        // Essay submissions
        $essaySubmissions = EssaySubmission::count();
        $gradedEssays = EssaySubmission::whereNotNull('graded_at')->count();

        // Recent activities
        $recentCourses = Course::with('instructors')
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = User::latest()
            ->take(5)
            ->get();

        $recentDiscussionsList = Discussion::with(['user', 'content.lesson.course'])
            ->latest()
            ->take(5)
            ->get();

        // Monthly enrollment trend (last 12 months)
        $monthlyEnrollments = DB::table('course_user')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return [
            'users' => [
                'total' => $totalUsers,
                'participants' => $totalParticipants,
                'instructors' => $totalInstructors,
                'event_organizers' => $totalEventOrganizers,
            ],
            'courses' => [
                'total' => $totalCourses,
                'published' => $publishedCourses,
                'draft' => $draftCourses,
                'recent_enrollments' => $recentEnrollments,
            ],
            'discussions' => [
                'total' => $totalDiscussions,
                'recent' => $recentDiscussions,
            ],
            'quizzes' => [
                'total' => $totalQuizzes,
                'attempts' => $quizAttempts,
                'completed' => $completedQuizzes,
            ],
            'essays' => [
                'submissions' => $essaySubmissions,
                'graded' => $gradedEssays,
            ],
            'recent_activities' => [
                'courses' => $recentCourses,
                'users' => $recentUsers,
                'discussions' => $recentDiscussionsList,
            ],
            'trends' => [
                'monthly_enrollments' => $monthlyEnrollments,
            ],
        ];
    }

    private function getParticipantStats($user)
    {
        // Get enrolled courses - using the correct relationship
        $enrolledCourses = $user->courses()->with(['lessons.contents', 'instructors'])->get();
        $courseIds = $enrolledCourses->pluck('id');

        // Course statistics
        $totalCourses = $enrolledCourses->count();
        $completedCourses = 0;
        $inProgressCourses = 0;

        // Content and progress statistics
        $totalContents = 0;
        $completedContents = 0;
        $totalLessons = 0;
        $completedLessons = 0;

        // Calculate progress for each course
        $courseProgress = $enrolledCourses->map(function ($course) use ($user, &$totalContents, &$completedContents, &$totalLessons, &$completedLessons, &$completedCourses, &$inProgressCourses) {
            $courseTotalContents = $course->lessons->sum(function ($lesson) {
                return $lesson->contents->count();
            });

            $courseCompletedContents = $user->completedContents()
                ->whereHas('lesson', function ($query) use ($course) {
                    $query->where('course_id', $course->id);
                })->count();

            $courseTotalLessons = $course->lessons->count();
            $courseCompletedLessons = $user->completedLessons()
                ->whereIn('lesson_id', $course->lessons->pluck('id'))
                ->count();

            $progress = $courseTotalContents > 0 ? round(($courseCompletedContents / $courseTotalContents) * 100, 1) : 0;

            // Update totals
            $totalContents += $courseTotalContents;
            $completedContents += $courseCompletedContents;
            $totalLessons += $courseTotalLessons;
            $completedLessons += $courseCompletedLessons;

            // Determine course status
            if ($progress >= 100) {
                $completedCourses++;
                $status = 'completed';
            } elseif ($progress > 0) {
                $inProgressCourses++;
                $status = 'in_progress';
            } else {
                $status = 'not_started';
            }

            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'thumbnail' => $course->thumbnail,
                'progress' => $progress,
                'status' => $status,
                'total_lessons' => $courseTotalLessons,
                'completed_lessons' => $courseCompletedLessons,
                'total_contents' => $courseTotalContents,
                'completed_contents' => $courseCompletedContents,
                'instructors' => $course->instructors,
                'created_at' => $course->created_at,
            ];
        })->sortByDesc('progress');

        // Quiz statistics
        $quizAttempts = QuizAttempt::where('user_id', $user->id)->count();
        $completedQuizzes = QuizAttempt::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->count();
        $passedQuizzes = QuizAttempt::where('user_id', $user->id)
            ->where('passed', true)
            ->count();

        // Essay submissions
        $essaySubmissions = EssaySubmission::where('user_id', $user->id)->count();
        $gradedEssays = EssaySubmission::where('user_id', $user->id)
            ->whereNotNull('graded_at')
            ->count();

        // Recent activity - make sure we handle the case where no courses exist
        $recentCompletions = collect();
        if ($courseIds->isNotEmpty()) {
            $recentCompletions = DB::table('content_user')
                ->join('contents', 'content_user.content_id', '=', 'contents.id')
                ->join('lessons', 'contents.lesson_id', '=', 'lessons.id')
                ->join('courses', 'lessons.course_id', '=', 'courses.id')
                ->where('content_user.user_id', $user->id)
                ->where('content_user.completed', true)
                ->where('content_user.created_at', '>=', now()->subDays(7))
                ->whereIn('courses.id', $courseIds)
                ->select('contents.title as content_title', 'lessons.title as lesson_title', 'courses.title as course_title', 'content_user.created_at')
                ->orderBy('content_user.created_at', 'desc')
                ->limit(5)
                ->get();
        }

        // Upcoming or recommended content
        $nextContents = collect();
        foreach ($enrolledCourses as $course) {
            $nextContent = $course->lessons()
                ->with('contents')
                ->get()
                ->flatMap(function ($lesson) use ($user) {
                    return $lesson->contents->filter(function ($content) use ($user) {
                        return !$user->completedContents->contains($content->id);
                    });
                })
                ->take(3);

            $nextContents = $nextContents->merge($nextContent);
        }

        // Discussion activity
        $userDiscussions = Discussion::where('user_id', $user->id)->count();
        $userReplies = DB::table('discussion_replies')->where('user_id', $user->id)->count();

        // Calculate overall progress
        $overallProgress = $totalContents > 0 ? round(($completedContents / $totalContents) * 100, 1) : 0;

        return [
            'courses' => [
                'total' => $totalCourses,
                'completed' => $completedCourses,
                'in_progress' => $inProgressCourses,
                'not_started' => $totalCourses - $completedCourses - $inProgressCourses,
                'progress' => $courseProgress,
                'overall_progress' => $overallProgress,
            ],
            'content' => [
                'total_contents' => $totalContents,
                'completed_contents' => $completedContents,
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
            ],
            'quizzes' => [
                'attempts' => $quizAttempts,
                'completed' => $completedQuizzes,
                'passed' => $passedQuizzes,
            ],
            'essays' => [
                'submissions' => $essaySubmissions,
                'graded' => $gradedEssays,
            ],
            'discussions' => [
                'started' => $userDiscussions,
                'replies' => $userReplies,
            ],
            'recent_activities' => [
                'completions' => $recentCompletions,
                'next_contents' => $nextContents->take(5),
            ],
        ];
    }

    private function getInstructorStats($user)
    {
        // Get courses taught by this instructor
        $instructorCourses = Course::whereHas('instructors', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['lessons.contents', 'enrolledUsers', 'lessons.quizzes'])->get();

        $courseIds = $instructorCourses->pluck('id');

        // Course statistics
        $totalCourses = $instructorCourses->count();
        $publishedCourses = $instructorCourses->where('status', 'published')->count();
        $draftCourses = $instructorCourses->where('status', 'draft')->count();

        // Student statistics
        $totalStudents = DB::table('course_user')
            ->whereIn('course_id', $courseIds)
            ->distinct('user_id')
            ->count();

        $recentEnrollments = DB::table('course_user')
            ->whereIn('course_id', $courseIds)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // Content statistics
        $totalLessons = $instructorCourses->sum(function ($course) {
            return $course->lessons->count();
        });

        $totalContents = $instructorCourses->sum(function ($course) {
            return $course->lessons->sum(function ($lesson) {
                return $lesson->contents->count();
            });
        });

        // Quiz statistics
        $totalQuizzes = Quiz::whereHas('lesson.course.instructors', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        $quizAttempts = QuizAttempt::whereHas('quiz.lesson.course.instructors', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        $completedQuizzes = QuizAttempt::whereHas('quiz.lesson.course.instructors', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereNotNull('completed_at')->count();

        // Discussion statistics
        $totalDiscussions = Discussion::whereHas('content.lesson.course.instructors', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        $recentDiscussions = Discussion::whereHas('content.lesson.course.instructors', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('created_at', '>=', now()->subDays(7))->count();

        // Essay submissions for grading
        $pendingEssays = EssaySubmission::whereHas('content.lesson.course.instructors', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereNull('graded_at')->count();

        $totalEssaySubmissions = EssaySubmission::whereHas('content.lesson.course.instructors', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        // Recent activities
        $recentCourses = $instructorCourses->sortByDesc('created_at')->take(3);

        $recentStudents = User::whereHas('courses', function ($query) use ($courseIds) {
            $query->whereIn('course_id', $courseIds);
        })->with('courses')->latest()->take(5)->get();

        $recentDiscussionsList = Discussion::whereHas('content.lesson.course.instructors', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['user', 'content.lesson.course'])->latest()->take(5)->get();

        // Course performance data
        $coursePerformance = $instructorCourses->map(function ($course) {
            $totalStudents = $course->enrolledUsers->count();
            $totalContents = $course->lessons->sum(function ($lesson) {
                return $lesson->contents->count();
            });

            if ($totalStudents > 0 && $totalContents > 0) {
                $completedContents = DB::table('content_user')
                    ->join('contents', 'content_user.content_id', '=', 'contents.id')
                    ->join('lessons', 'contents.lesson_id', '=', 'lessons.id')
                    ->where('lessons.course_id', $course->id)
                    ->where('content_user.completed', true)
                    ->count();

                $averageProgress = round(($completedContents / ($totalStudents * $totalContents)) * 100, 1);
            } else {
                $averageProgress = 0;
            }

            return [
                'id' => $course->id,
                'title' => $course->title,
                'students' => $totalStudents,
                'progress' => $averageProgress,
                'status' => $course->status,
                'created_at' => $course->created_at,
            ];
        });

        return [
            'courses' => [
                'total' => $totalCourses,
                'published' => $publishedCourses,
                'draft' => $draftCourses,
                'performance' => $coursePerformance,
            ],
            'students' => [
                'total' => $totalStudents,
                'recent_enrollments' => $recentEnrollments,
            ],
            'content' => [
                'lessons' => $totalLessons,
                'contents' => $totalContents,
            ],
            'quizzes' => [
                'total' => $totalQuizzes,
                'attempts' => $quizAttempts,
                'completed' => $completedQuizzes,
            ],
            'discussions' => [
                'total' => $totalDiscussions,
                'recent' => $recentDiscussions,
            ],
            'essays' => [
                'pending' => $pendingEssays,
                'total' => $totalEssaySubmissions,
            ],
            'recent_activities' => [
                'courses' => $recentCourses,
                'students' => $recentStudents,
                'discussions' => $recentDiscussionsList,
            ],
        ];
    }

    private function getEoStats(User $user): array
    {
        // 1. Ambil semua kursus yang dikelola oleh EO ini
        $managedCourses = $user->eventOrganizedCourses()->with(['enrolledUsers', 'lessons.contents'])->get();
        $courseIds = $managedCourses->pluck('id');

        // 2. Statistik Kursus
        $totalCourses = $managedCourses->count();
        $publishedCourses = $managedCourses->where('status', 'published')->count();
        $draftCourses = $managedCourses->where('status', 'draft')->count();

        // 3. Statistik Peserta
        $totalParticipants = DB::table('course_user')
            ->whereIn('course_id', $courseIds)
            ->distinct('user_id')
            ->count();
        
        $recentEnrollments = DB::table('course_user')
            ->whereIn('course_id', $courseIds)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // 4. Statistik Aktivitas
        $totalDiscussions = Discussion::whereHas('content.lesson.course', function ($query) use ($courseIds) {
            $query->whereIn('id', $courseIds);
        })->count();

        // 5. Performa Kursus (Ringkasan per kursus)
        $coursePerformance = $managedCourses->map(function ($course) {
            $participantCount = $course->enrolledUsers->count();
            $totalContents = $course->lessons->sum(fn($lesson) => $lesson->contents->count());
            
            $averageProgress = 0;
            if ($participantCount > 0 && $totalContents > 0) {
                // ✅ FIX: Menggunakan whereIn untuk mencocokkan dengan array/collection ID pelajaran
                $completedContentsCount = DB::table('content_user')
                    ->join('contents', 'content_user.content_id', '=', 'contents.id')
                    ->whereIn('contents.lesson_id', $course->lessons->pluck('id'))
                    ->where('content_user.completed', true)
                    ->count();
                
                $averageProgress = round(($completedContentsCount / ($participantCount * $totalContents)) * 100, 1);
            }

            return [
                'title' => $course->title,
                'status' => $course->status,
                'participants' => $participantCount,
                'progress' => $averageProgress,
            ];
        });

        // 6. Aktivitas Terbaru
        $recentUsers = User::whereHas('enrolledCourses', function ($query) use ($courseIds) {
            $query->whereIn('course_id', $courseIds);
        })->where('created_at', '>=', now()->subDays(7))->latest()->take(5)->get();


        // 7. Susun data untuk dikirim ke view
        return [
            'overview' => [
                ['label' => 'Kursus Dikelola', 'value' => $totalCourses, 'icon' => 'briefcase'],
                ['label' => 'Total Peserta', 'value' => $totalParticipants, 'icon' => 'users'],
                ['label' => 'Pendaftar Baru (30 Hari)', 'value' => $recentEnrollments, 'icon' => 'user-plus'],
                ['label' => 'Total Diskusi', 'value' => $totalDiscussions, 'icon' => 'chat-bubble-left-right'],
            ],
            'course_summary' => [
                'published' => $publishedCourses,
                'draft' => $draftCourses,
            ],
            'course_performance' => $coursePerformance,
            'recent_activity' => [
                'users' => $recentUsers,
            ]
        ];
    }
}
