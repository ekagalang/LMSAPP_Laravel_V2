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
use App\Models\Certificate;
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

        // Get announcements for all users with error handling
        $announcements = $this->getDashboardAnnouncements($user);

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

            $completedCertificates = Certificate::where('user_id', $user->id)
                ->with('course') // Eager load relasi course
                ->latest('issued_at')
                ->get();
            // <-- LOGIKA BARU SELESAI -->

            return view('dashboard.participant', compact('stats', 'announcements', 'completedCertificates'));
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());

            // Fallback dengan stats kosong
            $stats = $this->getEmptyStats();
            $announcements = collect(); // Empty collection sebagai fallback
            $completedCertificates = collect();

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

                        // Check completion based on content type
                        $isCompleted = false;
                        if ($content->type === 'quiz' && $content->quiz_id) {
                            // For quiz content, check if passed
                            $isCompleted = $user->quizAttempts()
                                ->where('quiz_id', $content->quiz_id)
                                ->where('passed', true)
                                ->exists();
                        } elseif ($content->type === 'essay') {
                            // For essay content, check if submitted
                            $isCompleted = $user->essaySubmissions()
                                ->where('content_id', $content->id)
                                ->exists();
                        } else {
                            // For regular content, check completion table
                            $isCompleted = $user->completedContents()->where('content_id', $content->id)->exists();
                        }

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
                        $isCompleted = false;
                        if ($content->type === 'quiz' && $content->quiz_id) {
                            $isCompleted = $user->quizAttempts()
                                ->where('quiz_id', $content->quiz_id)
                                ->where('passed', true)
                                ->exists();
                        } elseif ($content->type === 'essay') {
                            $isCompleted = $user->essaySubmissions()
                                ->where('content_id', $content->id)
                                ->exists();
                        } else {
                            $isCompleted = $user->completedContents()->where('content_id', $content->id)->exists();
                        }

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
            Log::error('Error in getParticipantStats: ' . $e->getMessage());
            return $this->getEmptyStats();
        }
    }

    private function getInstructorStats($user)
    {
        try {
            // Get courses taught by this instructor
            $instructorCourses = Course::whereHas('instructors', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['enrolledUsers'])->get();

            $courseIds = $instructorCourses->pluck('id');

            // Get upcoming zoom sessions for instructor's courses
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
                ->with(['lesson.course'])
                ->orderBy('scheduled_start', 'asc')
                ->take(5)
                ->get();

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
            $coursePerformance = $instructorCourses->map(function ($course) use ($user) {
                // Get participants only from periods assigned to this instructor
                $instructorPeriods = $user->instructorPeriods()
                    ->where('course_id', $course->id)
                    ->pluck('course_periods.id');
                
                if ($instructorPeriods->isNotEmpty()) {
                    // Get participants only from instructor's assigned periods
                    $participants = User::whereHas('participantPeriods', function ($query) use ($instructorPeriods) {
                        $query->whereIn('course_periods.id', $instructorPeriods);
                    })->get();
                } else {
                    // Fallback to course-level participants if no periods assigned
                    $participants = $course->enrolledUsers;
                }
                
                $totalStudents = $participants->count();
                $averageProgress = 0;

                if ($totalStudents > 0) {
                    // Panggil fungsi getProgressForCourse yang sudah benar untuk setiap peserta
                    $totalProgressSum = $participants->sum(function ($participant) use ($course) {
                        return $participant->getProgressForCourse($course)['progress_percentage'];
                    });
                    // Hitung rata-ratanya
                    $averageProgress = round($totalProgressSum / $totalStudents);
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
                'upcoming_zoom_sessions' => $upcomingZoomSessions,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getInstructorStats: ' . $e->getMessage());
            return $this->getEmptyStats();
        }
    }

    private function getEoStats(User $user): array
    {
        try {
            // 1. Ambil semua kursus yang dikelola oleh EO ini
            $managedCourses = $user->eventOrganizedCourses()->with(['enrolledUsers'])->get();
            $courseIds = $managedCourses->pluck('id');

            // Get upcoming zoom sessions for EO's managed courses
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
                ->with(['lesson.course'])
                ->orderBy('scheduled_start', 'asc')
                ->take(5)
                ->get();

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

            // 5. ✅ PERBAIKAN: Performa Kursus (Ringkasan per kursus)
            $coursePerformance = $managedCourses->map(function ($course) {
                $participantCount = $course->enrolledUsers->count();
                $averageProgress = 0;

                if ($participantCount > 0) {
                    // Panggil fungsi getProgressForCourse yang sudah benar untuk setiap peserta
                    $totalProgressSum = $course->enrolledUsers->sum(function ($participant) use ($course) {
                        return $participant->getProgressForCourse($course)['progress_percentage'];
                    });
                    // Hitung rata-ratanya
                    $averageProgress = round($totalProgressSum / $participantCount);
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
                ],
                'upcoming_zoom_sessions' => $upcomingZoomSessions,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getEoStats: ' . $e->getMessage());
            return $this->getEmptyStats();
        }
    }
}
