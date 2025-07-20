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
use App\Models\Announcement;
use App\Models\AnnouncementRead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        // ✅ PERBAIKAN: Get announcements for all users with better error handling
        $announcements = $this->getAnnouncementsForUser($user);

        try {
            if ($user->hasRole('super-admin')) {
                $stats = $this->getAdminStats();
                return view('dashboard.admin', compact('stats', 'announcements'));
            }

            if ($user->hasRole('instructor')) {
                $stats = $this->getInstructorStats($user);
                return view('dashboard.instructor', compact('stats', 'announcements'));
            }

            if ($user->hasRole('event-organizer')) {
                $stats = $this->getEoStats($user);
                return view('dashboard.eo', compact('stats', 'announcements'));
            }

            // For Participant
            $stats = $this->getParticipantStats($user);
            return view('dashboard.participant', compact('stats', 'announcements'));
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());

            // Fallback dengan stats kosong
            $stats = $this->getEmptyStats();

            if ($user->hasRole('super-admin')) {
                return view('dashboard.admin', compact('stats', 'announcements'));
            } elseif ($user->hasRole('instructor')) {
                return view('dashboard.instructor', compact('stats', 'announcements'));
            } elseif ($user->hasRole('event-organizer')) {
                return view('dashboard.eo', compact('stats', 'announcements'));
            } else {
                return view('dashboard.participant', compact('stats', 'announcements'));
            }
        }
    }

    /**
     * ✅ PERBAIKAN: Get announcements for user with better error handling
     */
    private function getAnnouncementsForUser($user)
    {
        try {
            // Check if announcements table exists
            if (!DB::getSchemaBuilder()->hasTable('announcements')) {
                Log::info('Announcements table does not exist');
                return collect(); // Return empty collection
            }

            // Check if Announcement model exists and is accessible
            if (!class_exists('App\Models\Announcement')) {
                Log::info('Announcement model does not exist');
                return collect();
            }

            // Try to get announcements with basic query first
            $announcements = Announcement::where('is_active', true)
                ->where(function ($query) {
                    $query->where('published_at', '<=', now())
                        ->orWhereNull('published_at');
                })
                ->where(function ($query) {
                    $query->where('expires_at', '>', now())
                        ->orWhereNull('expires_at');
                })
                ->with('user:id,name')
                ->latest()
                ->take(10)
                ->get();

            // Filter announcements for current user based on target roles
            $userRoles = $user->getRoleNames()->toArray();
            $filteredAnnouncements = $announcements->filter(function ($announcement) use ($userRoles) {
                // If no target roles specified, show to all users
                if (empty($announcement->target_roles)) {
                    return true;
                }

                // Check if user has any of the target roles
                foreach ($userRoles as $role) {
                    if (in_array($role, $announcement->target_roles)) {
                        return true;
                    }
                }

                return false;
            });

            // Add read status and other metadata
            $filteredAnnouncements->transform(function ($announcement) use ($user) {
                try {
                    $announcement->is_read_by_user = $announcement->isReadByUser($user);
                } catch (\Exception $e) {
                    $announcement->is_read_by_user = false;
                }
                return $announcement;
            });

            Log::info('Successfully loaded ' . $filteredAnnouncements->count() . ' announcements for user ' . $user->id);
            return $filteredAnnouncements;
        } catch (\Exception $e) {
            Log::error('Error fetching announcements: ' . $e->getMessage());
            return collect(); // Return empty collection as fallback
        }
    }

    /**
     * ✅ PERBAIKAN: Get announcement statistics with better error handling
     */
    private function getAnnouncementStats()
    {
        try {
            // Check if table exists
            if (!DB::getSchemaBuilder()->hasTable('announcements')) {
                return [
                    'total' => 0,
                    'active' => 0,
                    'recent' => 0,
                    'unread' => 0,
                ];
            }

            $total = DB::table('announcements')->count();
            $active = DB::table('announcements')->where('is_active', true)->count();
            $recent = DB::table('announcements')
                ->where('created_at', '>=', now()->subWeek())
                ->count();

            // Get unread count for current user
            $unread = 0;
            if (Auth::check()) {
                try {
                    $unread = Announcement::unreadForUser(Auth::user())->count();
                } catch (\Exception $e) {
                    $unread = 0;
                }
            }

            return [
                'total' => $total,
                'active' => $active,
                'recent' => $recent,
                'unread' => $unread,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getAnnouncementStats: ' . $e->getMessage());
            return [
                'total' => 0,
                'active' => 0,
                'recent' => 0,
                'unread' => 0,
            ];
        }
    }

    // Rest of the methods remain the same...
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
                'unread' => 0,
            ],
            'students' => [
                'total' => 0,
                'recent_enrollments' => 0,
            ],
            'recent_activities' => [
                'courses' => collect(),
                'users' => collect(),
                'discussions' => collect(),
                'completions' => collect(),
                'next_contents' => collect(),
                'students' => collect(),
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
        ];
    }

    private function getAdminStats()
    {
        try {
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

            // Announcement statistics
            $announcementStats = $this->getAnnouncementStats();

            // Recent activities
            $recentCourses = Course::with('instructors')
                ->latest()
                ->take(5)
                ->get();

            $recentUsers = User::with(['roles' => function ($query) {
                $query->select('name');
            }])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($user) {
                    $user->primary_role = $user->roles->first()?->name ?? 'participant';
                    return $user;
                });

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
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Error in getAdminStats: ' . $e->getMessage());
            return $this->getEmptyStats();
        }
    }

    private function getInstructorStats($user)
    {
        try {
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
            $totalStudents = 0;
            if ($courseIds->isNotEmpty()) {
                $totalStudents = DB::table('course_user')
                    ->whereIn('course_id', $courseIds)
                    ->distinct('user_id')
                    ->count();
            }

            $recentEnrollments = 0;
            if ($courseIds->isNotEmpty()) {
                $recentEnrollments = DB::table('course_user')
                    ->whereIn('course_id', $courseIds)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count();
            }

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
            ];
        } catch (\Exception $e) {
            Log::error('Error in getInstructorStats: ' . $e->getMessage());
            return $this->getEmptyStats();
        }
    }

    private function getParticipantStats($user)
    {
        try {
            // Get enrolled courses
            $enrolledCourses = $user->courses()->with(['lessons.contents', 'instructors'])->get();

            // Course statistics
            $totalCourses = $enrolledCourses->count();

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

            // Discussion activity
            $userDiscussions = Discussion::where('user_id', $user->id)->count();

            // Calculate progress and completion statistics
            $completedCourses = 0;
            $inProgressCourses = 0;
            $totalContents = 0;
            $completedContents = 0;
            $totalLessons = 0;
            $completedLessons = 0;

            // Calculate progress for each course
            $courseProgress = $enrolledCourses->map(function ($course) use ($user, &$totalContents, &$completedContents, &$totalLessons, &$completedLessons, &$completedCourses, &$inProgressCourses) {
                $courseTotalContents = 0;
                $courseCompletedContents = 0;

                foreach ($course->lessons as $lesson) {
                    foreach ($lesson->contents as $content) {
                        $courseTotalContents++;
                        $isCompleted = $user->completedContents()
                            ->where('content_id', $content->id)
                            ->exists();

                        if ($isCompleted) {
                            $courseCompletedContents++;
                        }
                    }
                }

                $courseTotalLessons = $course->lessons->count();
                $courseCompletedLessons = 0;

                foreach ($course->lessons as $lesson) {
                    $lessonTotalContents = $lesson->contents->count();
                    $lessonCompletedContents = 0;

                    foreach ($lesson->contents as $content) {
                        $isCompleted = $user->completedContents()
                            ->where('content_id', $content->id)
                            ->exists();

                        if ($isCompleted) {
                            $lessonCompletedContents++;
                        }
                    }

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

            // Recent completions
            $recentCompletions = collect();
            if ($enrolledCourses->isNotEmpty()) {
                $courseIds = $enrolledCourses->pluck('id');
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
                ],
                'recent_activities' => [
                    'completions' => $recentCompletions,
                    'next_contents' => collect(),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Error in getParticipantStats: ' . $e->getMessage());
            return $this->getEmptyStats();
        }
    }

    private function getEoStats(User $user): array
    {
        try {
            $managedCourses = Course::whereHas('instructors', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['enrolledUsers', 'lessons.contents'])->get();

            $courseIds = $managedCourses->pluck('id');

            $totalCourses = $managedCourses->count();
            $publishedCourses = $managedCourses->where('status', 'published')->count();
            $draftCourses = $managedCourses->where('status', 'draft')->count();

            $totalParticipants = 0;
            $recentEnrollments = 0;

            if ($courseIds->isNotEmpty()) {
                $totalParticipants = DB::table('course_user')
                    ->whereIn('course_id', $courseIds)
                    ->distinct('user_id')
                    ->count();

                $recentEnrollments = DB::table('course_user')
                    ->whereIn('course_id', $courseIds)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count();
            }

            $totalDiscussions = 0;
            if ($courseIds->isNotEmpty()) {
                $totalDiscussions = Discussion::whereHas('content.lesson.course', function ($query) use ($courseIds) {
                    $query->whereIn('id', $courseIds);
                })->count();
            }

            $coursePerformance = $managedCourses->map(function ($course) {
                $participantCount = $course->enrolledUsers->count();
                $totalContents = $course->lessons->sum(fn($lesson) => $lesson->contents->count());

                $averageProgress = 0;
                if ($participantCount > 0 && $totalContents > 0) {
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

            $recentUsers = collect();
            if ($courseIds->isNotEmpty()) {
                $recentUsers = User::whereHas('courses', function ($query) use ($courseIds) {
                    $query->whereIn('course_id', $courseIds);
                })->where('created_at', '>=', now()->subDays(7))->latest()->take(5)->get();
            }

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
        } catch (\Exception $e) {
            Log::error('Error in getEoStats: ' . $e->getMessage());
            return $this->getEmptyStats();
        }
    }
}
