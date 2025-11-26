<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Course;
use App\Models\Content;
use App\Models\Discussion;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\EssaySubmission;
use App\Models\Feedback;
use App\Models\Announcement;
use App\Models\Certificate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        // Get announcements for all users with error handling
        $announcements = $this->getDashboardAnnouncements($user);

        try {
            // Map role -> dashboard view name
            $roleViewMap = [
                'super-admin' => 'dashboard.admin',
                'instructor' => 'dashboard.instructor',
                'event-organizer' => 'dashboard.eo',
                'participant' => 'dashboard.participant',
            ];

            // Decide target view based on first matching role in map
            $targetView = null;
            foreach ($user->getRoleNames() as $roleName) {
                if (isset($roleViewMap[$roleName])) {
                    $targetView = $roleViewMap[$roleName];
                    break;
                }
            }

            // Compute stats by capability and choose view; map directly by capability
            if (\Gate::check('admin-only')) {
                $stats = $this->getAdminStats();
                return view('dashboard.admin', compact('stats', 'announcements'));
            } elseif ($user->can('manage own courses')) {
                $stats = $this->getInstructorStats($user);
                return view('dashboard.instructor', compact('stats', 'announcements'));
            } elseif ($user->can('view progress reports') || $user->can('view certificate management')) {
                $stats = $this->getEoStats($user);
                return view('dashboard.eo', compact('stats', 'announcements'));
            } elseif ($user->can('attempt quizzes')) {
                $stats = $this->getParticipantStats($user);

                // Get certificates that have been issued
                $completedCertificates = Certificate::where('user_id', $user->id)
                    ->with('course')
                    ->latest('issued_at')
                    ->get();

                // Get courses that are eligible for certificate but not yet generated
                $eligibleForCertificates = $user->courses()
                    ->whereNotNull('certificate_template_id')
                    ->with(['certificateTemplate', 'lessons.contents'])
                    ->get()
                    ->filter(function ($course) use ($user) {
                        return $user->isEligibleForCertificate($course)
                            && !$user->hasCertificateForCourse($course);
                    });

                return view('dashboard.participant', compact('stats', 'announcements', 'completedCertificates', 'eligibleForCertificates'));
            } else {
                // Unknown/custom role: use generic stats and view
                $stats = $this->getGenericStats($user);
                return view('dashboard.generic', compact('stats', 'announcements'));
            }
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());

            // Fallback dengan stats kosong
            $stats = $this->getEmptyStats();
            $announcements = collect(); // Empty collection sebagai fallback
            $completedCertificates = collect();

            if (\Gate::check('admin-only')) {
                return view('dashboard.admin', compact('stats', 'announcements'));
            } elseif ($user->can('manage own courses')) {
                return view('dashboard.instructor', compact('stats', 'announcements'));
            } elseif ($user->can('view progress reports') || $user->can('view certificate management')) {
                return view('dashboard.eo', compact('stats', 'announcements'));
            } elseif ($user->can('attempt quizzes')) {
                $completedCertificates = collect();
                $eligibleForCertificates = collect();
                return view('dashboard.participant', compact('stats', 'announcements', 'completedCertificates', 'eligibleForCertificates'));
            } else {
                return view('dashboard.generic', compact('stats', 'announcements'));
            }
        }
    }

    /**
     * Get announcements for user with error handling
     * 🚨 PERBAIKAN: Gunakan paginate() jika ingin menggunakan .total() di view
     */
    private function getDashboardAnnouncements($user)
    {
        try {
            // Gunakan scope forUser yang sudah kita perbaiki
            return Announcement::forUser($user)
                ->latest()
                ->take(1) // <-- Ambil hanya satu
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching announcements for dashboard: ' . $e->getMessage());
            return collect(); // Kembalikan koleksi kosong jika error
        }
    }

    private function getEmptyStats()
    {
        return [
            'users' => [
                'total' => 0,
                'participants' => 0,
                'instructors' => 0,
                'event_organizers' => 0,
            ],
            'courses' => [
                'total' => 0,
                'published' => 0,
                'draft' => 0,
                'recent_enrollments' => 0,
                'overall_progress' => 0,
                'completed' => 0,
                'in_progress' => 0,
                'not_started' => 0,
                'progress' => collect(),
                'performance' => collect(),
            ],
            'content' => [
                'total_contents' => 0,
                'completed_contents' => 0,
                'total_lessons' => 0,
                'completed_lessons' => 0,
                'lessons' => 0,
                'contents' => 0,
            ],
            'discussions' => [
                'total' => 0,
                'recent' => 0,
                'started' => 0,
                'replies' => 0,
            ],
            'quizzes' => [
                'total' => 0,
                'attempts' => 0,
                'completed' => 0,
                'passed' => 0,
            ],
            'essays' => [
                'submissions' => 0,
                'graded' => 0,
                'pending' => 0,
                'total' => 0,
            ],
            'announcements' => [
                'total' => 0,
                'active' => 0,
                'recent' => 0,
            ],
            'students' => [
                'total' => 0,
                'recent_enrollments' => 0,
            ],
            // For admin, instructor, and participant dashboards (plural)
            'recent_activities' => [
                'courses' => collect(),
                'users' => collect(),
                'discussions' => collect(),
                'completions' => collect(),
                'next_contents' => collect(),
                'students' => collect(),
            ],
            // For EO dashboard (singular)
            'recent_activity' => [
                'users' => collect(),
            ],
            'trends' => [
                'monthly_enrollments' => collect(),
            ],
            'overview' => [
                ['label' => 'Data', 'value' => 0, 'icon' => 'chart'],
            ],
            'course_summary' => [
                'published' => 0,
                'draft' => 0,
            ],
            'course_performance' => collect(),
            'upcoming_zoom_sessions' => collect(),
        ];
    }

    private function getGenericStats(User $user)
    {
        // Use participant stats as a reasonable default for custom roles
        try {
            return $this->getParticipantStats($user);
        } catch (\Throwable $t) {
            Log::error('Error in getGenericStats for user ' . $user->id . ': ' . $t->getMessage());
            Log::error('Stack trace: ' . $t->getTraceAsString());
            return $this->getEmptyStats();
        }
    }

    private function getAdminStats()
    {
        try {
            // Total users by capability (role-agnostic)
            $totalUsers = User::count();
            $totalParticipants = User::permission('attempt quizzes')->count();
            $totalInstructors = User::permission('manage own courses')->count();
            $totalEventOrganizers = User::permission('view progress reports')->count();

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

            // Announcement statistics with error handling
            $announcementStats = $this->getAnnouncementStats();

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
                'announcements' => $announcementStats,
                'recent_activities' => [
                    'courses' => $recentCourses,
                    'users' => $recentUsers,
                    'discussions' => $recentDiscussionsList,
                ],
                'trends' => [
                    'monthly_enrollments' => $monthlyEnrollments,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Error in getAdminStats: ' . $e->getMessage());
            return $this->getEmptyStats();
        }
    }

    /**
     * Get announcement statistics with error handling
     */
    private function getAnnouncementStats()
    {
        try {
            // Check if table exists and has required columns
            if (!DB::getSchemaBuilder()->hasTable('announcements')) {
                return [
                    'total' => 0,
                    'active' => 0,
                    'recent' => 0,
                ];
            }

            $columns = DB::getSchemaBuilder()->getColumnListing('announcements');

            $total = Announcement::count();
            $recent = Announcement::where('created_at', '>=', now()->subWeek())->count();

            // Only check is_active if column exists
            $active = 0;
            if (in_array('is_active', $columns)) {
                $active = Announcement::where('is_active', true)->count();
            } else {
                // Fallback: assume all announcements are active
                $active = $total;
            }

            return [
                'total' => $total,
                'active' => $active,
                'recent' => $recent,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getAnnouncementStats: ' . $e->getMessage());
            return [
                'total' => 0,
                'active' => 0,
                'recent' => 0,
            ];
        }
    }

    // Rest of the methods remain the same but with added error handling...
    private function getParticipantStats($user)
    {
        try {
            // Safety check: ensure user has the courses relationship
            if (!method_exists($user, 'courses')) {
                Log::warning('User model does not have courses() method for user ID: ' . $user->id);
                return $this->getEmptyStats();
            }

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
                // Improved progress calculation
                $courseTotalContents = 0;
                $courseCompletedContents = 0;

                foreach ($course->lessons as $lesson) {
                    foreach ($lesson->contents as $content) {
                        $courseTotalContents++;

                        // Check completion based on content type and optional status
                        $isCompleted = $this->isContentCompletedByUser($user, $content);

                        if ($isCompleted) {
                            $courseCompletedContents++;
                        }
                    }
                }

                $courseTotalLessons = $course->lessons->count();

                // Lesson completion calculation
                $courseCompletedLessons = 0;
                foreach ($course->lessons as $lesson) {
                    $lessonTotalContents = $lesson->contents->count();
                    $lessonCompletedContents = 0;

                    foreach ($lesson->contents as $content) {
                        $isCompleted = $this->isContentCompletedByUser($user, $content);

                        if ($isCompleted) {
                            $lessonCompletedContents++;
                        }
                    }

                    // Lesson is completed if all its contents are completed
                    if ($lessonTotalContents > 0 && $lessonCompletedContents >= $lessonTotalContents) {
                        $courseCompletedLessons++;
                    }
                }

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

            // Recent activity
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
                            // Skip konten opsional dari rekomendasi (agar tidak terlihat sebagai PR)
                            if ($content->is_optional ?? false) {
                                return false;
                            }
                            // Check if not completed based on content type
                            if ($content->type === 'quiz' && $content->quiz_id) {
                                return !$user->quizAttempts()
                                    ->where('quiz_id', $content->quiz_id)
                                    ->where('passed', true)
                                    ->exists();
                            } elseif ($content->type === 'essay') {
                                return !$user->essaySubmissions()
                                    ->where('content_id', $content->id)
                                    ->exists();
                            } else {
                                return !$user->contents()->where('content_id', $content->id)->exists();
                            }
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
        } catch (\Exception $e) {
            Log::error('Error in getParticipantStats for user ' . $user->id . ': ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return $this->getEmptyStats();
        }
    }

    private function isContentCompletedByUser(User $user, Content $content): bool
    {
        if (($content->is_optional ?? false)) {
            return $user->completedContents()
                ->where('content_id', $content->id)
                ->wherePivot('completed', true)
                ->exists();
        }

        if ($content->type === 'quiz' && $content->quiz_id) {
            return $user->quizAttempts()
                ->where('quiz_id', $content->quiz_id)
                ->where('passed', true)
                ->exists();
        }

        if ($content->type === 'essay') {
            return $user->essaySubmissions()
                ->where('content_id', $content->id)
                ->exists();
        }

        return $user->completedContents()
            ->where('content_id', $content->id)
            ->wherePivot('completed', true)
            ->exists();
    }

    private function getInstructorStats($user)
    {
        try {
            return Cache::remember('instructor_stats_' . $user->id, 60, function () use ($user) {
                // Minimal per-course list (limit to 10 entries)
                $coursesList = Course::whereHas('instructors', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->select('id', 'title', 'status', 'created_at')
                    ->withCount('enrolledUsers')
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get();

                // Course counters
                $totalCourses = Course::whereHas('instructors', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->count();
                $publishedCourses = Course::whereHas('instructors', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->where('status', 'published')
                    ->count();
                $draftCourses = $totalCourses - $publishedCourses;

                // Students (participants) totals and recent enrollments (last 30 days)
                $totalStudents = DB::table('course_user')
                    ->join('courses', 'course_user.course_id', '=', 'courses.id')
                    ->join('course_instructor', 'courses.id', '=', 'course_instructor.course_id')
                    ->where('course_instructor.user_id', $user->id)
                    ->distinct('course_user.user_id')
                    ->count('course_user.user_id');

                $recentEnrollments = DB::table('course_user')
                    ->join('courses', 'course_user.course_id', '=', 'courses.id')
                    ->join('course_instructor', 'courses.id', '=', 'course_instructor.course_id')
                    ->where('course_instructor.user_id', $user->id)
                    ->where('course_user.created_at', '>=', now()->subDays(30))
                    ->count();

                // Content totals
                $totalLessons = DB::table('lessons')
                    ->join('courses', 'lessons.course_id', '=', 'courses.id')
                    ->join('course_instructor', 'courses.id', '=', 'course_instructor.course_id')
                    ->where('course_instructor.user_id', $user->id)
                    ->count();

                $totalContents = DB::table('contents')
                    ->join('lessons', 'contents.lesson_id', '=', 'lessons.id')
                    ->join('courses', 'lessons.course_id', '=', 'courses.id')
                    ->join('course_instructor', 'courses.id', '=', 'course_instructor.course_id')
                    ->where('course_instructor.user_id', $user->id)
                    ->count('contents.id');

                // Quiz stats
                $totalQuizzes = DB::table('quizzes')
                    ->join('lessons', 'quizzes.lesson_id', '=', 'lessons.id')
                    ->join('courses', 'lessons.course_id', '=', 'courses.id')
                    ->join('course_instructor', 'courses.id', '=', 'course_instructor.course_id')
                    ->where('course_instructor.user_id', $user->id)
                    ->count();

                $quizAttempts = DB::table('quiz_attempts')
                    ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
                    ->join('lessons', 'quizzes.lesson_id', '=', 'lessons.id')
                    ->join('courses', 'lessons.course_id', '=', 'courses.id')
                    ->join('course_instructor', 'courses.id', '=', 'course_instructor.course_id')
                    ->where('course_instructor.user_id', $user->id)
                    ->count();

                $completedQuizzes = DB::table('quiz_attempts')
                    ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
                    ->join('lessons', 'quizzes.lesson_id', '=', 'lessons.id')
                    ->join('courses', 'lessons.course_id', '=', 'courses.id')
                    ->join('course_instructor', 'courses.id', '=', 'course_instructor.course_id')
                    ->where('course_instructor.user_id', $user->id)
                    ->whereNotNull('quiz_attempts.completed_at')
                    ->count();

                // Discussion stats
                $totalDiscussions = DB::table('discussions')
                    ->join('contents', 'discussions.content_id', '=', 'contents.id')
                    ->join('lessons', 'contents.lesson_id', '=', 'lessons.id')
                    ->join('courses', 'lessons.course_id', '=', 'courses.id')
                    ->join('course_instructor', 'courses.id', '=', 'course_instructor.course_id')
                    ->where('course_instructor.user_id', $user->id)
                    ->count();

                $recentDiscussions = DB::table('discussions')
                    ->join('contents', 'discussions.content_id', '=', 'contents.id')
                    ->join('lessons', 'contents.lesson_id', '=', 'lessons.id')
                    ->join('courses', 'lessons.course_id', '=', 'courses.id')
                    ->join('course_instructor', 'courses.id', '=', 'course_instructor.course_id')
                    ->where('course_instructor.user_id', $user->id)
                    ->where('discussions.created_at', '>=', now()->subDays(7))
                    ->count();

                // Essay stats
                $pendingEssays = DB::table('essay_submissions')
                    ->join('contents', 'essay_submissions.content_id', '=', 'contents.id')
                    ->join('lessons', 'contents.lesson_id', '=', 'lessons.id')
                    ->join('courses', 'lessons.course_id', '=', 'courses.id')
                    ->join('course_instructor', 'courses.id', '=', 'course_instructor.course_id')
                    ->where('course_instructor.user_id', $user->id)
                    ->whereNull('essay_submissions.graded_at')
                    ->count();

                $totalEssaySubmissions = DB::table('essay_submissions')
                    ->join('contents', 'essay_submissions.content_id', '=', 'contents.id')
                    ->join('lessons', 'contents.lesson_id', '=', 'lessons.id')
                    ->join('courses', 'lessons.course_id', '=', 'courses.id')
                    ->join('course_instructor', 'courses.id', '=', 'course_instructor.course_id')
                    ->where('course_instructor.user_id', $user->id)
                    ->count();

                // Upcoming zoom sessions (limit 5)
                $upcomingZoomSessions = \App\Models\Content::where('type', 'zoom')
                    ->whereHas('lesson.course.instructors', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->where(function ($query) {
                        $query->where('is_scheduled', false)
                            ->orWhere(function ($subQuery) {
                                $subQuery->where('is_scheduled', true)
                                    ->where('scheduled_start', '>=', now())
                                    ->where('scheduled_start', '<=', now()->addDays(7));
                            });
                    })
                    ->with(['lesson.course:id,title'])
                    ->orderBy('scheduled_start', 'asc')
                    ->take(5)
                    ->get(['id', 'lesson_id', 'title', 'body', 'is_scheduled', 'scheduled_start', 'scheduled_end']);

                // Recent activities (minimal and limited)
                $recentCourses = Course::whereHas('instructors', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->orderByDesc('created_at')
                    ->limit(3)
                    ->get(['id', 'title', 'created_at']);

                $recentStudents = User::select(
                        'users.id',
                        'users.name',
                        'users.email',
                        DB::raw('MAX(course_user.created_at) as last_enrolled_at')
                    )
                    ->join('course_user', 'users.id', '=', 'course_user.user_id')
                    ->join('courses', 'course_user.course_id', '=', 'courses.id')
                    ->join('course_instructor', 'courses.id', '=', 'course_instructor.course_id')
                    ->where('course_instructor.user_id', $user->id)
                    ->groupBy('users.id', 'users.name', 'users.email')
                    ->orderByDesc('last_enrolled_at')
                    ->limit(5)
                    ->get();

                $recentDiscussionsList = Discussion::with(['user:id,name,email', 'content:id,title'])
                    ->whereHas('content.lesson.course.instructors', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->latest()
                    ->take(5)
                    ->get(['id', 'user_id', 'content_id', 'created_at']);

                $coursePerformance = $coursesList->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'students' => $course->enrolled_users_count,
                        'progress' => null,
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
                    'upcoming_zoom_sessions' => $upcomingZoomSessions,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error in getInstructorStats: ' . $e->getMessage());
            return $this->getEmptyStats();
        }
    }

    private function getEoStats(User $user): array
    {
        try {
            return Cache::remember('eo_stats_' . $user->id, 60, function () use ($user) {
                // Ensure user has eventOrganizedCourses method
                if (!method_exists($user, 'eventOrganizedCourses')) {
                    Log::error('User model missing eventOrganizedCourses method for EO user: ' . $user->id);
                    throw new \Exception('Missing eventOrganizedCourses relationship');
                }

                // Upcoming zoom sessions with error handling
                $upcomingZoomSessions = collect();
                try {
                    $upcomingZoomSessions = \App\Models\Content::where('type', 'zoom')
                        ->whereHas('lesson.course.eventOrganizers', function ($query) use ($user) {
                            $query->where('user_id', $user->id);
                        })
                        ->where(function ($query) {
                            $query->where('is_scheduled', false)
                                ->orWhere(function ($subQuery) {
                                    $subQuery->where('is_scheduled', true)
                                        ->where('scheduled_start', '>=', now())
                                        ->where('scheduled_start', '<=', now()->addDays(7));
                                });
                        })
                        ->with(['lesson.course:id,title'])
                        ->orderBy('scheduled_start', 'asc')
                        ->take(5)
                        ->get(['id', 'lesson_id', 'title', 'body', 'is_scheduled', 'scheduled_start', 'scheduled_end']);
                } catch (\Exception $e) {
                    Log::warning('Error fetching zoom sessions for EO: ' . $e->getMessage());
                }

                // Course stats
                $totalCourses = $user->eventOrganizedCourses()->count();
                $publishedCourses = $user->eventOrganizedCourses()->where('status', 'published')->count();
                $draftCourses = $totalCourses - $publishedCourses;

                // Lightweight per-course list for display (limit to 10)
                $eoCoursesList = $user->eventOrganizedCourses()
                    ->select('id', 'title', 'status')
                    ->withCount('enrolledUsers')
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get();

                // Participant stats with error handling
                $totalParticipants = 0;
                $recentEnrollments = 0;
                try {
                    $totalParticipants = DB::table('course_user')
                        ->join('courses', 'course_user.course_id', '=', 'courses.id')
                        ->join('course_event_organizer', 'courses.id', '=', 'course_event_organizer.course_id')
                        ->where('course_event_organizer.user_id', $user->id)
                        ->distinct('course_user.user_id')
                        ->count('course_user.user_id');

                    $recentEnrollments = DB::table('course_user')
                        ->join('courses', 'course_user.course_id', '=', 'courses.id')
                        ->join('course_event_organizer', 'courses.id', '=', 'course_event_organizer.course_id')
                        ->where('course_event_organizer.user_id', $user->id)
                        ->where('course_user.created_at', '>=', now()->subDays(30))
                        ->count();
                } catch (\Exception $e) {
                    Log::warning('Error fetching participant stats for EO: ' . $e->getMessage());
                }

                // Discussion stats with error handling
                $totalDiscussions = 0;
                try {
                    $totalDiscussions = DB::table('discussions')
                        ->join('contents', 'discussions.content_id', '=', 'contents.id')
                        ->join('lessons', 'contents.lesson_id', '=', 'lessons.id')
                        ->join('courses', 'lessons.course_id', '=', 'courses.id')
                        ->join('course_event_organizer', 'courses.id', '=', 'course_event_organizer.course_id')
                        ->where('course_event_organizer.user_id', $user->id)
                        ->count();
                } catch (\Exception $e) {
                    Log::warning('Error fetching discussion stats for EO: ' . $e->getMessage());
                }

                // Recent users (by enrollments in last 7 days) with error handling
                $recentUsers = collect();
                try {
                    $recentUsers = User::select(
                            'users.id',
                            'users.name',
                            'users.email',
                            DB::raw('MAX(course_user.created_at) as last_enrolled_at')
                        )
                        ->join('course_user', 'users.id', '=', 'course_user.user_id')
                        ->join('courses', 'course_user.course_id', '=', 'courses.id')
                        ->join('course_event_organizer', 'courses.id', '=', 'course_event_organizer.course_id')
                        ->where('course_event_organizer.user_id', $user->id)
                        ->where('course_user.created_at', '>=', now()->subDays(7))
                        ->groupBy('users.id', 'users.name', 'users.email')
                        ->orderByDesc('last_enrolled_at')
                        ->limit(5)
                        ->get();
                } catch (\Exception $e) {
                    Log::warning('Error fetching recent users for EO: ' . $e->getMessage());
                }

                return [
                    'overview' => [
                        ['label' => 'Kursus Dikelola', 'value' => $totalCourses, 'icon' => 'briefcase'],
                        ['label' => 'Total Peserta', 'value' => $totalParticipants, 'icon' => 'users'],
                        ['label' => 'Pendaftar Baru (30 Hari)', 'value' => $recentEnrollments, 'icon' => 'user-plus'],
                        ['label' => 'Total Diskusi', 'value' => $totalDiscussions, 'icon' => 'chat-bubble-left-right'],
                    ],
                    'courses' => [
                        'total' => $totalCourses,
                    ],
                    'course_summary' => [
                        'published' => $publishedCourses,
                        'draft' => $draftCourses,
                    ],
                    // Keep a small list for the UI (capped at 10)
                    'course_performance' => $eoCoursesList->map(function ($c) {
                        return [
                            'title' => $c->title,
                            'status' => $c->status,
                            'participants' => $c->enrolled_users_count,
                            'progress' => null,
                        ];
                    }),
                    'students' => [
                        'total' => $totalParticipants,
                    ],
                    'recent_activity' => [
                        'users' => $recentUsers,
                    ],
                    'upcoming_zoom_sessions' => $upcomingZoomSessions,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error in getEoStats for user ' . $user->id . ': ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Clear cache for this user to prevent repeated errors
            try {
                Cache::forget('eo_stats_' . $user->id);
            } catch (\Exception $cacheError) {
                Log::error('Failed to clear cache for EO user: ' . $cacheError->getMessage());
            }

            return $this->getEmptyStats();
        }
    }
}
