<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\CertificateTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreCourseRequest;
use App\Services\CourseService;
use PDF;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    protected CourseService $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->middleware('auth')->except('show');
        $this->courseService = $courseService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Course::class);

        $user = Auth::user();
        $query = Course::query();

        // Admin/Super Admin - lihat semua kursus
        if ($user->can('manage all courses')) {
            // No filter needed, show all courses
        }
        // Instructor - lihat kursus yang dia ajar
        elseif ($user->can('manage own courses')) {
            $query->whereHas('instructors', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        // Event Organizer - lihat kursus yang dia kelola
        elseif ($user->can('view progress reports')) {
            $query->whereHas('eventOrganizers', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        // Participant - lihat kursus yang dia ikuti (enrolled) dan published
        else {
            $query->where('status', 'published')
                ->whereHas('enrolledUsers', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
        }

        $courses = $query->with('instructors')->latest()->paginate(10);

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $this->authorize('create', Course::class);
        $templates = CertificateTemplate::all(); // Ambil semua template
        return view('courses.create', compact('templates')); // Kirim ke view
    }

    public function store(StoreCourseRequest $request)
    {
        $this->authorize('create', Course::class);

        $validatedData = $request->validated();

        $request->validate([
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',
        ]);

        try {
            DB::beginTransaction(); // ← DB sudah di-import di atas

            // Handle thumbnail
            if ($request->hasFile('thumbnail')) {
                $validatedData['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            // Create course
            $course = Course::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'objectives' => $validatedData['objectives'],
                'thumbnail' => $validatedData['thumbnail'] ?? null,
                'status' => $validatedData['status'],
                'certificate_template_id' => $validatedData['certificate_template_id'] ?? null,
            ]);

            // Assign creator as instructor
            $course->instructors()->attach(Auth::id());

            if ($request->boolean('enable_periods')) {
                if (
                    $request->boolean('create_default_period') ||
                    (!empty($validatedData['periods']) && is_array($validatedData['periods']) && count($validatedData['periods']) > 0)
                ) {
                    $this->courseService->createCoursePeriods($course, $validatedData);
                }
            }

            // ✅ LOG COURSE CREATION
            \App\Models\ActivityLog::log('course_created', [
                'description' => "Created course: {$course->title}",
                'metadata' => [
                    'course_id' => $course->id,
                    'course_title' => $course->title,
                    'status' => $course->status,
                    'has_thumbnail' => !empty($course->thumbnail),
                    'certificate_template_id' => $course->certificate_template_id,
                    'periods_enabled' => $request->boolean('enable_periods'),
                ]
            ]);

            DB::commit();

            return redirect()->route('courses.show', $course)
                ->with('success', 'Kursus berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($validatedData['thumbnail'])) {
                Storage::disk('public')->delete($validatedData['thumbnail']);
            }

            return back()->withInput()->withErrors(['error' => 'Gagal membuat kursus: ' . $e->getMessage()]);
        }
    }


    public function show(Course $course)
    {
        $this->authorize('view', $course);
        $user = Auth::user();

        // [LOGIKA BARU] Jika pengguna adalah peserta, coba arahkan langsung ke materi pertama.
        if ($user && $user->can('attempt quizzes')) {
            // Pastikan peserta terdaftar di kursus ini untuk bisa langsung masuk
            if ($course->enrolledUsers->contains($user)) {
                // Urutkan lesson berdasarkan 'order' jika ada, jika tidak pakai 'id'
                $firstLesson = $course->lessons()->orderBy('order', 'asc')->orderBy('id', 'asc')->first();

                if ($firstLesson) {
                    // Urutkan content berdasarkan 'order' jika ada, jika tidak pakai 'id'
                    $firstContent = $firstLesson->contents()->orderBy('order', 'asc')->orderBy('id', 'asc')->first();

                    // Jika content pertama ditemukan, langsung redirect.
                    if ($firstContent) {
                        return redirect()->route('contents.show', $firstContent);
                    }
                }
            }
        }

        // Jika bukan peserta, atau jika tidak ada content, atau jika tidak terdaftar,
        // tampilkan halaman detail seperti biasa.
        $course->load('lessons.contents', 'instructors', 'enrolledUsers');

        // Filter periods for instructors - only show periods they are assigned to
        if ($user->isInstructorFor($course) && !$user->can('manage all courses') && !$user->isEventOrganizerFor($course)) {
            // Replace the periods relation with filtered periods
            $course->setRelation('periods', $course->getUserInstructorPeriods($user->id));
        } else {
            // Load all periods for super-admin, event-organizer, or other roles
            $course->load('periods');
        }

        $availableInstructors = User::permission('manage own courses')
            ->whereNotIn('id', $course->instructors->pluck('id'))
            ->orderBy('name')
            ->get();

        $unEnrolledParticipants = User::permission('attempt quizzes')
            ->whereDoesntHave('courses', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->orderBy('name')
            ->get();

        $availableOrganizers = User::permission('view progress reports')
            ->whereNotIn('id', $course->eventOrganizers->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('courses.show', compact('course', 'availableInstructors', 'availableOrganizers', 'unEnrolledParticipants'));
    }

    public function edit(Course $course)
    {
        $this->authorize('update', $course);
        $templates = CertificateTemplate::all(); // <-- AMBIL SEMUA TEMPLATE
        return view('courses.edit', compact('course', 'templates')); // <-- KIRIM TEMPLATE KE VIEW
    }

    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
            'clear_thumbnail' => 'nullable|boolean',
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',
            'enable_periods' => 'nullable|boolean',
            'periods_to_delete' => 'nullable|array',
            'periods_to_delete.*' => 'exists:course_classes,id',
            'periods' => 'nullable|array',
            'periods.*.id' => 'nullable|exists:course_classes,id',
            'periods.*.name' => 'required_with:periods|string|max:255',
            'periods.*.start_date' => 'nullable|date',
            'periods.*.end_date' => 'nullable|date|after:periods.*.start_date',
            'periods.*.description' => 'nullable|string',
            'periods.*.max_participants' => 'nullable|integer|min:1',
            'periods.*.status' => 'required_with:periods|in:upcoming,active,completed,cancelled',
        ]);

        try {
            DB::beginTransaction();

            // ✅ ENHANCED LOGGING: Capture original data
            $originalData = $course->getOriginal();

            if ($request->boolean('clear_thumbnail')) {
                if ($course->thumbnail) {
                    Storage::disk('public')->delete($course->thumbnail);
                }
                $validatedData['thumbnail'] = null;
            } elseif ($request->hasFile('thumbnail')) {
                if ($course->thumbnail) {
                    Storage::disk('public')->delete($course->thumbnail);
                }
                $validatedData['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            $course->update($validatedData);

            if ($request->boolean('enable_periods')) {
                $this->updateCoursePeriods($course, $request, $validatedData);
            } else {
                $course->periods()->delete();
            }

            // ✅ LOG COURSE UPDATE WITH BEFORE/AFTER
            $changes = [];
            $fields = ['title', 'description', 'objectives', 'status', 'thumbnail', 'certificate_template_id'];

            foreach ($fields as $field) {
                if ($originalData[$field] != $course->$field) {
                    $changes[$field] = [
                        'before' => $originalData[$field],
                        'after' => $course->$field
                    ];
                }
            }

            \App\Models\ActivityLog::log('course_updated', [
                'description' => "Updated course: {$course->title}" . (count($changes) > 0 ? " (" . implode(', ', array_keys($changes)) . " changed)" : ""),
                'metadata' => [
                    'course_id' => $course->id,
                    'course_title' => $course->title,
                    'status' => $course->status,
                    'changes' => $changes,
                    'changed_fields' => array_keys($changes),
                    'thumbnail_changed' => $request->hasFile('thumbnail') || $request->boolean('clear_thumbnail'),
                    'periods_enabled' => $request->boolean('enable_periods'),
                ]
            ]);

            DB::commit();
            return redirect()->route('courses.index')->with('success', 'Kursus berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui kursus: ' . $e->getMessage()]);
        }
    }

    private function updateCoursePeriods(Course $course, Request $request, array $validatedData)
    {
        // 1. Delete marked periods
        if (isset($validatedData['periods_to_delete'])) {
            $course->periods()->whereIn('id', $validatedData['periods_to_delete'])->delete();
        }

        // 2. Update/Create periods
        if (isset($validatedData['periods'])) {
            foreach ($validatedData['periods'] as $periodData) {
                if (isset($periodData['id']) && $periodData['id']) {
                    // Update existing period
                    $course->periods()->where('id', $periodData['id'])->update([
                        'name' => $periodData['name'],
                        'start_date' => $periodData['start_date'],
                        'end_date' => $periodData['end_date'],
                        'description' => $periodData['description'] ?? null,
                        'max_participants' => $periodData['max_participants'] ?? null,
                        'status' => $periodData['status'],
                    ]);
                } else {
                    // Create new period
                    $course->periods()->create([
                        'name' => $periodData['name'],
                        'start_date' => $periodData['start_date'],
                        'end_date' => $periodData['end_date'],
                        'description' => $periodData['description'] ?? null,
                        'max_participants' => $periodData['max_participants'] ?? null,
                        'status' => $periodData['status'],
                    ]);
                }
            }
        }
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);

        // Store data before deletion for logging
        $courseData = [
            'course_id' => $course->id,
            'course_title' => $course->title,
            'status' => $course->status,
            'total_lessons' => $course->lessons()->count(),
            'total_participants' => $course->enrolledUsers()->count(),
        ];

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }
        $course->delete();

        // ✅ LOG COURSE DELETION
        \App\Models\ActivityLog::log('course_deleted', [
            'description' => "Deleted course: {$courseData['course_title']}",
            'metadata' => $courseData
        ]);

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil dihapus.');
    }

    /**
     * PERUBAHAN: Menambahkan metode untuk mendaftarkan peserta.
     */
    public function enrollParticipant(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Gunakan syncWithoutDetaching untuk menambahkan user tanpa menghapus yang sudah ada
        $course->enrolledUsers()->syncWithoutDetaching($request->user_ids);

        // ✅ LOG PARTICIPANT ENROLLMENT
        $enrolledUsers = User::whereIn('id', $request->user_ids)->get(['id', 'name', 'email']);
        \App\Models\ActivityLog::log('participants_enrolled', [
            'description' => "Enrolled " . count($request->user_ids) . " participant(s) to course: {$course->title}",
            'metadata' => [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'participant_count' => count($request->user_ids),
                'participants' => $enrolledUsers->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email])->toArray(),
            ]
        ]);

        return redirect()->back()->with('success', 'Peserta berhasil didaftarkan.');
    }

    /**
     * PERUBAHAN: Menambahkan metode untuk mencabut akses peserta.
     */
    public function unenrollParticipants(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Get user data before detaching for logging
        $unenrolledUsers = User::whereIn('id', $request->user_ids)->get(['id', 'name', 'email']);

        // Gunakan detach untuk menghapus hubungan antara kursus dan user
        $course->enrolledUsers()->detach($request->user_ids);

        // ✅ LOG PARTICIPANT UNENROLLMENT
        \App\Models\ActivityLog::log('participants_unenrolled', [
            'description' => "Unenrolled " . count($request->user_ids) . " participant(s) from course: {$course->title}",
            'metadata' => [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'participant_count' => count($request->user_ids),
                'participants' => $unenrolledUsers->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email])->toArray(),
            ]
        ]);

        return redirect()->back()->with('success', 'Akses peserta berhasil dicabut.');
    }

    public function showProgress(Course $course, Request $request)
    {
        $this->authorize('viewProgress', $course);

        $user = Auth::user();

        // ✅ FIX: Update query logic untuk course dropdown (permission/relationship based)
        if ($user->can('manage all courses')) {
            $instructorCourses = Course::with('instructors')->orderBy('title')->get();
        } else {
            $instructorCourses = $user->taughtCourses()->with('instructors')->orderBy('title')->get()
                ->merge($user->eventOrganizedCourses()->with('instructors')->orderBy('title')->get())
                ->unique('id')
                ->values();
        }

        // ✅ FIX: Ensure current course is included in dropdown
        if (!$instructorCourses->contains('id', $course->id)) {
            // If current course is not in the list, add it
            $instructorCourses->push($course);
        }

        // ✅ FIX: Buat base query untuk analytics (tanpa search/pagination)
        // Filter participants by instructor's assigned periods
        if ($user->isInstructorFor($course) && !$user->can('manage all courses') && !$user->isEventOrganizerFor($course)) {
            // Get periods where this instructor is assigned for this course
            $instructorPeriods = $user->instructorPeriods()
                ->where('course_id', $course->id)
                ->pluck('course_classes.id');

            if ($instructorPeriods->isNotEmpty()) {
                // Get participants only from instructor's assigned periods
                $baseQuery = User::whereHas('participantPeriods', function ($query) use ($instructorPeriods) {
                    $query->whereIn('course_classes.id', $instructorPeriods);
                });
            } else {
                // If instructor is not assigned to any periods, show course-level participants
                $baseQuery = $course->enrolledUsers();
            }
        } else {
            // For super-admin, event-organizer, or other roles, show all participants
            $baseQuery = $course->enrolledUsers();
        }

        // Clone base query untuk table (dengan search & pagination)
        $enrolledUsersQuery = clone $baseQuery;

        // Apply search filter HANYA untuk table list, TIDAK untuk analytics
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $enrolledUsersQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // ✅ OPTIMASI BESAR: Tambahkan pagination
        $perPage = $request->input('per_page', 50); // Default 50 per page

        // ✅ OPTIMASI: Eager load semua relasi yang dibutuhkan untuk menghitung progress
        $enrolledUsers = $enrolledUsersQuery
            ->with([
                'completedContents' => function ($query) use ($course) {
                    $query->whereHas('lesson', function ($q) use ($course) {
                        $q->where('course_id', $course->id);
                    })
                    ->wherePivot('completed', true)
                    ->with('lesson:id,title,course_id');
                },
                'quizAttempts' => function ($query) use ($course) {
                    $query->whereHas('quiz.lesson', function ($q) use ($course) {
                        $q->where('course_id', $course->id);
                    })
                    ->where('passed', true)
                    ->select('id', 'user_id', 'quiz_id', 'passed', 'completed_at');
                },
                'essaySubmissions' => function ($query) use ($course) {
                    $query->whereHas('content.lesson', function ($q) use ($course) {
                        $q->where('course_id', $course->id);
                    })
                    ->with(['answers' => function ($q) {
                        $q->select('id', 'submission_id', 'question_id', 'score', 'feedback');
                    }]);
                }
            ])
            ->orderBy('name')
            ->paginate($perPage);

        // ✅ OPTIMASI: Load course lessons & contents sekali saja (bukan per participant)
        $course->load(['lessons.contents.quiz', 'lessons.contents.essayQuestions']);

        // Pre-calculate total content count
        $totalContentCount = $course->lessons->sum(function ($lesson) {
            return $lesson->contents->count();
        });

        // ✅ OPTIMASI: Gunakan method yang lebih efisien untuk hitung progress
        $participantsProgress = $enrolledUsers->map(function ($participant) use ($course, $totalContentCount) {
            $progressData = $this->calculateUserProgressOptimized($participant, $course, $totalContentCount);

            // Get last completed content dari relasi yang sudah di-load
            $lastCompletedContent = $participant->completedContents
                ->sortByDesc('pivot.completed_at')
                ->first();

            $lastPosition = $lastCompletedContent && $lastCompletedContent->lesson
                ? $lastCompletedContent->lesson->title
                : 'Belum Memulai';

            return [
                'id' => $participant->id,
                'name' => $participant->name,
                'email' => $participant->email,
                'completed_count' => $progressData['completed_count'],
                'progress_percentage' => $progressData['progress_percentage'],
                'last_position' => $lastPosition,
                'last_lesson_id' => $lastCompletedContent && $lastCompletedContent->lesson
                    ? $lastCompletedContent->lesson->id
                    : null,
            ];
        });

        // ✅ ANALYTICS: Gunakan baseQuery (TANPA search filter) untuk analytics SEMUA peserta
        $analytics = $this->calculateProgressAnalytics($course, $baseQuery);

        return view('courses.progress', [
            'course' => $course,
            'instructorCourses' => $instructorCourses,
            'participantsProgress' => $participantsProgress,
            'totalContentCount' => $totalContentCount,
            'enrolledUsers' => $enrolledUsers, // Untuk pagination links
            'analytics' => $analytics, // Data analytics
        ]);
    }

    /**
     * ✅ ANALYTICS: Calculate progress analytics for all participants
     */
    private function calculateProgressAnalytics($course, $enrolledUsersQuery)
    {
        // Clone query untuk mendapatkan semua user (bukan hanya current page)
        $allUsers = (clone $enrolledUsersQuery)
            ->with([
                'completedContents' => function ($query) use ($course) {
                    $query->whereHas('lesson', function ($q) use ($course) {
                        $q->where('course_id', $course->id);
                    })
                    ->wherePivot('completed', true)
                    ->with('lesson:id,title,course_id,order');
                },
                'quizAttempts' => function ($query) use ($course) {
                    $query->whereHas('quiz.lesson', function ($q) use ($course) {
                        $q->where('course_id', $course->id);
                    })
                    ->where('passed', true);
                },
                'essaySubmissions' => function ($query) use ($course) {
                    $query->whereHas('content.lesson', function ($q) use ($course) {
                        $q->where('course_id', $course->id);
                    })
                    ->with(['answers' => function ($q) {
                        $q->select('id', 'submission_id', 'question_id', 'score', 'feedback');
                    }]);
                }
            ])
            ->get();

        $course->load(['lessons.contents.quiz', 'lessons.contents.essayQuestions']);

        $totalContentCount = $course->lessons->sum(function ($lesson) {
            return $lesson->contents->count();
        });

        // Progress distribution
        $progressDistribution = [
            '0-25' => 0,
            '26-50' => 0,
            '51-75' => 0,
            '76-99' => 0,
            '100' => 0,
        ];

        // Lesson position tracking
        $lessonPositions = [];
        $totalProgress = 0;

        foreach ($allUsers as $user) {
            $progressData = $this->calculateUserProgressOptimized($user, $course, $totalContentCount);
            $percentage = $progressData['progress_percentage'];
            $totalProgress += $percentage;

            // Categorize progress
            if ($percentage == 100) {
                $progressDistribution['100']++;
            } elseif ($percentage >= 76) {
                $progressDistribution['76-99']++;
            } elseif ($percentage >= 51) {
                $progressDistribution['51-75']++;
            } elseif ($percentage >= 26) {
                $progressDistribution['26-50']++;
            } else {
                $progressDistribution['0-25']++;
            }

            // Track last position
            $lastCompletedContent = $user->completedContents
                ->sortByDesc('pivot.completed_at')
                ->first();

            if ($lastCompletedContent && $lastCompletedContent->lesson) {
                $lessonId = $lastCompletedContent->lesson->id;
                $lessonTitle = $lastCompletedContent->lesson->title;

                if (!isset($lessonPositions[$lessonId])) {
                    $lessonPositions[$lessonId] = [
                        'title' => $lessonTitle,
                        'count' => 0,
                        'order' => $lastCompletedContent->lesson->order ?? 999,
                    ];
                }
                $lessonPositions[$lessonId]['count']++;
            }
        }

        // Sort lessons by count and get top 5
        uasort($lessonPositions, function($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        $topLessons = array_slice($lessonPositions, 0, 5, true);

        // Calculate averages
        $totalUsers = $allUsers->count();
        $averageProgress = $totalUsers > 0 ? round($totalProgress / $totalUsers, 1) : 0;

        return [
            'distribution' => $progressDistribution,
            'top_lessons' => $topLessons,
            'average_progress' => $averageProgress,
            'total_participants' => $totalUsers,
            'completed_participants' => $progressDistribution['100'],
            'in_progress_participants' => $totalUsers - $progressDistribution['100'],
        ];
    }

    /**
     * ✅ OPTIMASI: Method helper untuk menghitung progress tanpa N+1 queries
     * Menggunakan data yang sudah di-eager load
     */
    private function calculateUserProgressOptimized($user, $course, $totalContentCount)
    {
        if ($totalContentCount === 0) {
            return [
                'progress_percentage' => 0,
                'completed_count' => 0,
                'total_count' => 0,
            ];
        }

        $completedCount = 0;

        // Buat map untuk akses cepat O(1)
        $completedContentsMap = $user->completedContents->pluck('id')->flip();
        $quizAttemptsMap = $user->quizAttempts->pluck('quiz_id')->flip();
        $essaySubmissionsMap = $user->essaySubmissions->keyBy('content_id');

        foreach ($course->lessons as $lesson) {
            foreach ($lesson->contents as $content) {
                $isCompleted = false;

                // Check berdasarkan tipe content
                if ($content->is_optional ?? false) {
                    $isCompleted = $completedContentsMap->has($content->id);
                } elseif ($content->type === 'quiz' && $content->quiz_id) {
                    $isCompleted = $quizAttemptsMap->has($content->quiz_id);
                } elseif ($content->type === 'essay') {
                    $submission = $essaySubmissionsMap->get($content->id);
                    if ($submission) {
                        $isCompleted = $this->checkEssayCompletionOptimized($content, $submission);
                    }
                } else {
                    $isCompleted = $completedContentsMap->has($content->id);
                }

                if ($isCompleted) {
                    $completedCount++;
                }
            }
        }

        $progressPercentage = round(($completedCount / $totalContentCount) * 100, 2);

        return [
            'progress_percentage' => $progressPercentage,
            'completed_count' => $completedCount,
            'total_count' => $totalContentCount,
        ];
    }

    /**
     * ✅ OPTIMASI: Check essay completion tanpa query tambahan
     */
    private function checkEssayCompletionOptimized($content, $submission)
    {
        if (!$submission) {
            return false;
        }

        $totalQuestions = $content->essayQuestions->count();

        // Legacy system (no questions)
        if ($totalQuestions === 0) {
            return $submission->answers->count() > 0;
        }

        // Check if requires review
        if (!($content->requires_review ?? true)) {
            return $submission->answers->count() > 0;
        }

        // New system - check based on scoring and grading mode
        if (!$content->scoring_enabled) {
            // Without scoring
            if ($content->grading_mode === 'overall') {
                return $submission->answers->whereNotNull('feedback')->count() > 0;
            } else {
                return $submission->answers->whereNotNull('feedback')->count() >= $totalQuestions;
            }
        } else {
            // With scoring
            if ($content->grading_mode === 'overall') {
                return $submission->answers->whereNotNull('score')->count() > 0;
            } else {
                return $submission->answers->whereNotNull('score')->count() >= $totalQuestions;
            }
        }
    }

    public function showParticipantProgress(Course $course, User $user)
    {
        $this->authorize('viewProgress', $course);

        if (!$course->enrolledUsers()->where('user_id', $user->id)->exists()) {
            abort(404, 'Peserta tidak terdaftar pada kursus ini.');
        }

        // ✅ OPTIMASI: Eager load semua relasi yang dibutuhkan sekaligus untuk menghindari N+1
        $course->load([
            'lessons' => function ($query) {
                $query->orderBy('order');
            },
            'lessons.contents' => function ($query) {
                $query->orderBy('order');
            },
            'lessons.contents.essayQuestions',
            'lessons.contents.quiz'
        ]);

        // ✅ OPTIMASI: Load semua data user yang dibutuhkan sekaligus
        $user->load([
            'completedContents' => function ($query) use ($course) {
                $query->whereHas('lesson', function ($q) use ($course) {
                    $q->where('course_id', $course->id);
                })->wherePivot('completed', true);
            },
            'quizAttempts' => function ($query) use ($course) {
                $query->whereHas('quiz.lesson', function ($q) use ($course) {
                    $q->where('course_id', $course->id);
                });
            },
            'essaySubmissions' => function ($query) use ($course) {
                $query->whereHas('content.lesson', function ($q) use ($course) {
                    $q->where('course_id', $course->id);
                })->with(['answers' => function ($q) {
                    $q->select('id', 'submission_id', 'question_id', 'score', 'feedback');
                }]);
            }
        ]);

        // ✅ OPTIMASI: Buat map untuk akses cepat O(1)
        $completedContentsMap = $user->completedContents->pluck('id')->flip();
        $quizAttemptsMap = $user->quizAttempts->groupBy('quiz_id');
        $essaySubmissionsMap = $user->essaySubmissions->keyBy('content_id');

        // ✅ OPTIMASI: Pre-calculate status untuk semua content sekaligus
        $contentStatusData = collect();
        foreach ($course->lessons as $lesson) {
            foreach ($lesson->contents as $content) {
                $statusData = $this->calculateContentStatus(
                    $content,
                    $user,
                    $completedContentsMap,
                    $quizAttemptsMap,
                    $essaySubmissionsMap
                );
                $contentStatusData->put($content->id, $statusData);
            }
        }

        return view('courses.participant_progress', [
            'course' => $course,
            'participant' => $user,
            'lessons' => $course->lessons,
            'completedContentsMap' => $completedContentsMap,
            'contentStatusData' => $contentStatusData,
            'essaySubmissionsMap' => $essaySubmissionsMap
        ]);
    }

    /**
     * ✅ OPTIMASI: Helper method untuk menghitung status content tanpa query tambahan
     */
    private function calculateContentStatus($content, $user, $completedContentsMap, $quizAttemptsMap, $essaySubmissionsMap)
    {
        $status = 'not_started';
        $statusText = 'Belum Dimulai';
        $badgeClass = 'bg-gray-100 text-gray-800';
        $isCompleted = false;

        // Handle optional content
        if ($content->is_optional ?? false) {
            $isCompleted = $completedContentsMap->has($content->id);
            if ($isCompleted) {
                $status = 'completed';
                $statusText = 'Selesai';
                $badgeClass = 'bg-green-100 text-green-800';
            }
            return compact('status', 'statusText', 'badgeClass', 'isCompleted');
        }

        // Handle quiz content
        if ($content->type === 'quiz' && $content->quiz_id) {
            $passedAttempt = $quizAttemptsMap->has($content->quiz_id) &&
                             $quizAttemptsMap->get($content->quiz_id)->where('passed', true)->isNotEmpty();

            if ($passedAttempt) {
                $status = 'completed';
                $statusText = 'Selesai';
                $badgeClass = 'bg-green-100 text-green-800';
                $isCompleted = true;
            } else {
                $hasAttempt = $quizAttemptsMap->has($content->quiz_id);
                if ($hasAttempt) {
                    $status = 'failed';
                    $statusText = 'Belum Lulus';
                    $badgeClass = 'bg-red-100 text-red-800';
                }
            }
            return compact('status', 'statusText', 'badgeClass', 'isCompleted');
        }

        // Handle essay content
        if ($content->type === 'essay') {
            $submission = $essaySubmissionsMap->get($content->id);

            if (!$submission) {
                return compact('status', 'statusText', 'badgeClass', 'isCompleted');
            }

            $totalQuestions = $content->essayQuestions->count();

            // Legacy system (no questions)
            if ($totalQuestions === 0) {
                if ($submission->answers->count() > 0) {
                    $status = 'completed';
                    $statusText = 'Selesai';
                    $badgeClass = 'bg-green-100 text-green-800';
                    $isCompleted = true;
                }
                return compact('status', 'statusText', 'badgeClass', 'isCompleted');
            }

            // Check if requires review
            if (!($content->requires_review ?? true)) {
                if ($submission->answers->count() > 0) {
                    $status = 'completed';
                    $statusText = 'Selesai';
                    $badgeClass = 'bg-green-100 text-green-800';
                    $isCompleted = true;
                }
                return compact('status', 'statusText', 'badgeClass', 'isCompleted');
            }

            // New system - check based on scoring and grading mode
            if (!$content->scoring_enabled) {
                // Without scoring
                if ($content->grading_mode === 'overall') {
                    $answersWithFeedback = $submission->answers->whereNotNull('feedback')->count();
                    if ($answersWithFeedback > 0) {
                        $status = 'completed';
                        $statusText = 'Selesai';
                        $badgeClass = 'bg-green-100 text-green-800';
                        $isCompleted = true;
                    } elseif ($submission->answers->count() > 0) {
                        $status = 'pending_grade';
                        $statusText = 'Menunggu Penilaian';
                        $badgeClass = 'bg-yellow-100 text-yellow-800';
                    }
                } else {
                    $answersWithFeedback = $submission->answers->whereNotNull('feedback')->count();
                    if ($answersWithFeedback >= $totalQuestions) {
                        $status = 'completed';
                        $statusText = 'Selesai';
                        $badgeClass = 'bg-green-100 text-green-800';
                        $isCompleted = true;
                    } elseif ($submission->answers->count() > 0) {
                        $status = 'pending_grade';
                        $statusText = 'Menunggu Penilaian';
                        $badgeClass = 'bg-yellow-100 text-yellow-800';
                    }
                }
            } else {
                // With scoring
                if ($content->grading_mode === 'overall') {
                    $answersWithScore = $submission->answers->whereNotNull('score')->count();
                    if ($answersWithScore > 0) {
                        $status = 'completed';
                        $statusText = 'Selesai';
                        $badgeClass = 'bg-green-100 text-green-800';
                        $isCompleted = true;
                    } elseif ($submission->answers->count() > 0) {
                        $status = 'pending_grade';
                        $statusText = 'Menunggu Penilaian';
                        $badgeClass = 'bg-yellow-100 text-yellow-800';
                    }
                } else {
                    $gradedAnswers = $submission->answers->whereNotNull('score')->count();
                    if ($gradedAnswers >= $totalQuestions) {
                        $status = 'completed';
                        $statusText = 'Selesai';
                        $badgeClass = 'bg-green-100 text-green-800';
                        $isCompleted = true;
                    } elseif ($submission->answers->count() > 0) {
                        $status = 'pending_grade';
                        $statusText = 'Menunggu Penilaian';
                        $badgeClass = 'bg-yellow-100 text-yellow-800';
                    }
                }
            }
            return compact('status', 'statusText', 'badgeClass', 'isCompleted');
        }

        // Handle other content types (video, document, etc)
        $isCompleted = $completedContentsMap->has($content->id);
        if ($isCompleted) {
            $status = 'completed';
            $statusText = 'Selesai';
            $badgeClass = 'bg-green-100 text-green-800';
        }

        return compact('status', 'statusText', 'badgeClass', 'isCompleted');
    }

    public function addInstructor(Request $request, Course $course)
    {
        $this->authorize('update', $course);
        $request->validate(['user_ids' => 'required|array']);
        $instructorIds = User::whereIn('id', $request->user_ids)->role('instructor')->pluck('id');

        // Get instructor details for logging
        $addedInstructors = User::whereIn('id', $instructorIds)->get(['id', 'name', 'email']);

        $course->instructors()->syncWithoutDetaching($instructorIds);

        // ✅ LOG INSTRUCTOR ASSIGNMENT
        \App\Models\ActivityLog::log('instructor_added', [
            'description' => "Added " . count($addedInstructors) . " instructor(s) to course: {$course->title}",
            'metadata' => [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'instructor_count' => count($addedInstructors),
                'instructors' => $addedInstructors->map(fn($i) => [
                    'id' => $i->id,
                    'name' => $i->name,
                    'email' => $i->email
                ])->toArray(),
            ]
        ]);

        return back()->with('success', 'Instruktur berhasil ditambahkan.');
    }

    public function removeInstructor(Request $request, Course $course)
    {
        $this->authorize('update', $course);
        $request->validate(['user_ids' => 'required|array']);

        // Get instructor details before removal for logging
        $removedInstructors = User::whereIn('id', $request->user_ids)->get(['id', 'name', 'email']);

        $course->instructors()->detach($request->user_ids);

        // ✅ LOG INSTRUCTOR REMOVAL
        \App\Models\ActivityLog::log('instructor_removed', [
            'description' => "Removed " . count($removedInstructors) . " instructor(s) from course: {$course->title}",
            'metadata' => [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'instructor_count' => count($removedInstructors),
                'instructors' => $removedInstructors->map(fn($i) => [
                    'id' => $i->id,
                    'name' => $i->name,
                    'email' => $i->email
                ])->toArray(),
            ]
        ]);

        return back()->with('success', 'Instruktur berhasil dihapus.');
    }

    public function downloadProgressPdf(Course $course)
    {
        $this->authorize('viewProgress', $course);

        $course->load('lessons.contents', 'enrolledUsers');

        // Get all contents for this course
        $allContents = $course->lessons->flatMap(fn($l) => $l->contents);
        $totalContentCount = $allContents->count();

        $participantsProgress = $course->enrolledUsers->map(function ($participant) use ($course) {

            // USE THE SAME PROGRESS CALCULATION AS WEB DISPLAY
            $progressData = $participant->getProgressForCourse($course);

            // Get quiz scores for this participant
            $quizScores = $participant->quizAttempts()
                ->whereHas('quiz.lesson.course', function ($query) use ($course) {
                    $query->where('id', $course->id);
                })
                ->where('passed', true)
                ->with('quiz.questions')
                ->get()
                ->map(function ($attempt) {
                    return [
                        'quiz_title' => $attempt->quiz->title,
                        'score' => $attempt->score,
                        'max_score' => $attempt->quiz->questions->count(),
                        'percentage' => $attempt->quiz->questions->count() > 0
                            ? round(($attempt->score / $attempt->quiz->questions->count()) * 100, 2)
                            : 0
                    ];
                });

            // ✅ FIX: Essay scores using new schema
            $essayScores = collect();

            $essaySubmissions = $participant->essaySubmissions()
                ->whereHas('content.lesson.course', function ($query) use ($course) {
                    $query->where('id', $course->id);
                })
                ->with(['content', 'answers'])
                ->get();

            foreach ($essaySubmissions as $submission) {
                $answersWithScores = $submission->answers()->whereNotNull('score')->get();

                if ($answersWithScores->count() > 0) {
                    $averageScore = $answersWithScores->avg('score');
                    $totalQuestions = $submission->content->essayQuestions()->count();
                    $gradedQuestions = $answersWithScores->count();

                    $essayScores->push([
                        'essay_title' => $submission->content->title,
                        'score' => round($averageScore, 2),
                        'percentage' => round($averageScore, 2)
                    ]);
                }
            }

            return [
                'name' => $participant->name,
                'email' => $participant->email,
                'progress_percentage' => $progressData['progress_percentage'],
                'completed_count' => $progressData['completed_count'],
                'total_count' => $progressData['total_count'],
                'quiz_scores' => $quizScores,
                'essay_scores' => $essayScores,
                'quiz_average' => $quizScores->avg('percentage') ?? 0,
                'essay_average' => $essayScores->avg('percentage') ?? 0,
            ];
        });

        $data = [
            'course' => $course,
            'participantsProgress' => $participantsProgress,
            'date' => date('d M Y')
        ];

        $pdf = PDF::loadView('reports.progress_pdf', $data);

        return $pdf->download('laporan-progres-' . Str::slug($course->title) . '.pdf');
    }

    public function showScores(Course $course, Request $request)
    {
        $this->authorize('viewProgress', $course);

        $user = Auth::user();

        // Build course list for filter dropdown (permission-based)
        if ($user->can('manage all courses')) {
            $courseOptions = Course::with('instructors')->orderBy('title')->get();
        } else {
            $courseOptions = $user->taughtCourses()->with('instructors')->orderBy('title')->get()
                ->merge($user->eventOrganizedCourses()->with('instructors')->orderBy('title')->get())
                ->unique('id')->values();
        }
        if (!$courseOptions->contains('id', $course->id)) {
            $courseOptions->push($course);
        }

        // Filter participants by instructor periods (same logic as showProgress)
        if ($user->isInstructorFor($course) && !$user->can('manage all courses') && !$user->isEventOrganizerFor($course)) {
            $instructorPeriods = $user->instructorPeriods()
                ->where('course_id', $course->id)
                ->pluck('course_classes.id');

            if ($instructorPeriods->isNotEmpty()) {
                $participantsQuery = User::whereHas('participantPeriods', function ($query) use ($instructorPeriods) {
                    $query->whereIn('course_classes.id', $instructorPeriods);
                });
            } else {
                $participantsQuery = $course->enrolledUsers();
            }
        } else {
            $participantsQuery = $course->enrolledUsers();
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $participantsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $participants = $participantsQuery->orderBy('name')->get();

        // Get lessons with their quizzes for better organization
        $lessonsWithQuizzes = $course->lessons()
            ->with(['contents' => function ($query) {
                $query->where('type', 'quiz')
                      ->whereNotNull('quiz_id')
                      ->with(['quiz' => function ($q) {
                          $q->with('questions');
                      }])
                      ->orderBy('order');
            }])
            ->whereHas('contents', function ($query) {
                $query->where('type', 'quiz')->whereNotNull('quiz_id');
            })
            ->orderBy('order')
            ->get();

        $participantsData = $participants->map(function (User $participant) use ($course, $lessonsWithQuizzes) {
            $participantQuizData = [];
            $totalQuizAverage = 0;
            $totalQuizCount = 0;

            foreach ($lessonsWithQuizzes as $lesson) {
                $lessonQuizzes = [];
                
                foreach ($lesson->contents as $content) {
                    if ($content->type === 'quiz' && $content->quiz) {
                        $quiz = $content->quiz;
                        
                        // Get all attempts for this quiz by this participant
                        $attempts = $participant->quizAttempts()
                            ->where('quiz_id', $quiz->id)
                            ->orderByDesc('completed_at')
                            ->get();

                        $attemptsData = [];
                        $latestScore = 0;
                        $isPassed = false;

                        // ✅ FIX: Hitung pass_marks dari passing_percentage
                        // ⚠️ CRITICAL FIX: Load questions jika belum ter-load
                        if (!$quiz->relationLoaded('questions')) {
                            $quiz->load('questions');
                        }

                        $totalMarks = $quiz->questions->sum('marks') ?: 1;
                        $passMarks = ($totalMarks * ($quiz->passing_percentage ?? 70)) / 100;

                        foreach ($attempts as $index => $attempt) {
                            // ✅ FIX: total_marks sudah dihitung di atas
                            $percentage = $totalMarks > 0 ? round(($attempt->score / $totalMarks) * 100, 2) : 0;
                            $attemptPassed = $attempt->passed || ($passMarks > 0 && $attempt->score >= $passMarks);
                            
                            if ($index === 0) { // Latest attempt
                                $latestScore = $percentage;
                                $isPassed = $attemptPassed;
                            }
                            
                            $attemptsData[] = [
                                'attempt_number' => $attempts->count() - $index,
                                'score' => $attempt->score,
                                'total_marks' => $totalMarks,
                                'percentage' => $percentage,
                                'passed' => $attemptPassed,
                                'completed_at' => $attempt->completed_at,
                                'is_latest' => $index === 0
                            ];
                        }

                        if (count($attemptsData) > 0) {
                            $totalQuizAverage += $latestScore;
                            $totalQuizCount++;
                        }

                        $lessonQuizzes[] = [
                            'quiz_id' => $quiz->id,
                            'quiz_title' => $quiz->title,
                            'attempts' => $attemptsData,
                            'latest_score' => $latestScore,
                            'is_passed' => $isPassed,
                            'total_attempts' => count($attemptsData),
                            'pass_marks' => $passMarks,
                            'needs_retry' => !$isPassed && count($attemptsData) > 0
                        ];
                    }
                }

                if (count($lessonQuizzes) > 0) {
                    $participantQuizData[] = [
                        'lesson_id' => $lesson->id,
                        'lesson_title' => $lesson->title,
                        'lesson_order' => $lesson->order,
                        'quizzes' => $lessonQuizzes
                    ];
                }
            }

            // Progress summary for context
            $progressData = $participant->getProgressForCourse($course);
            $overallQuizAverage = $totalQuizCount > 0 ? round($totalQuizAverage / $totalQuizCount, 2) : 0;

            return [
                'id' => $participant->id,
                'name' => $participant->name,
                'email' => $participant->email,
                'quiz_data' => $participantQuizData,
                'overall_quiz_average' => $overallQuizAverage,
                'progress_percentage' => $progressData['progress_percentage'] ?? 0,
                'total_quiz_attempts' => collect($participantQuizData)->flatMap(function($lesson) {
                    return collect($lesson['quizzes'])->sum('total_attempts');
                })->sum()
            ];
        });

        return view('courses.scores', [
            'course' => $course,
            'courseOptions' => $courseOptions,
            'participantsData' => $participantsData,
            'lessonsWithQuizzes' => $lessonsWithQuizzes,
        ]);
    }

    public function addEventOrganizer(Request $request, Course $course)
    {
        $this->authorize('update', $course);
        $request->validate(['user_ids' => 'required|array']);
        $organizerIds = User::whereIn('id', $request->user_ids)->role('event-organizer')->pluck('id');

        // Get organizer details for logging
        $addedOrganizers = User::whereIn('id', $organizerIds)->get(['id', 'name', 'email']);

        $course->eventOrganizers()->syncWithoutDetaching($organizerIds);

        // ✅ LOG EVENT ORGANIZER ASSIGNMENT
        \App\Models\ActivityLog::log('event_organizer_added', [
            'description' => "Added " . count($addedOrganizers) . " Event Organizer(s) to course: {$course->title}",
            'metadata' => [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'organizer_count' => count($addedOrganizers),
                'organizers' => $addedOrganizers->map(fn($o) => [
                    'id' => $o->id,
                    'name' => $o->name,
                    'email' => $o->email
                ])->toArray(),
            ]
        ]);

        return back()->with('success', 'Event Organizer berhasil ditambahkan.');
    }

    public function removeEventOrganizer(Request $request, Course $course)
    {
        $this->authorize('update', $course);
        $request->validate(['user_ids' => 'required|array']);

        // Get organizer details before removal for logging
        $removedOrganizers = User::whereIn('id', $request->user_ids)->get(['id', 'name', 'email']);

        $course->eventOrganizers()->detach($request->user_ids);

        // ✅ LOG EVENT ORGANIZER REMOVAL
        \App\Models\ActivityLog::log('event_organizer_removed', [
            'description' => "Removed " . count($removedOrganizers) . " Event Organizer(s) from course: {$course->title}",
            'metadata' => [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'organizer_count' => count($removedOrganizers),
                'organizers' => $removedOrganizers->map(fn($o) => [
                    'id' => $o->id,
                    'name' => $o->name,
                    'email' => $o->email
                ])->toArray(),
            ]
        ]);

        return back()->with('success', 'Event Organizer berhasil dihapus.');
    }

    public function duplicate(Course $course)
    {
        // ✅ PERBAIKAN: Ganti otorisasi dari 'create' menjadi 'duplicate'
        $this->authorize('duplicate', Course::class);

        try {
            // ✅ FIX: Increase execution time untuk course dengan banyak content
            // Ini mencegah timeout pada course yang besar
            set_time_limit(300); // 5 menit

            // Load relations untuk logging dan validasi
            $course->loadCount(['lessons', 'contents']);

            // ✅ FIX: Add detailed logging untuk debugging
            \Log::info('Starting course duplication', [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'user_id' => Auth::id(),
                'lessons_count' => $course->lessons_count ?? 0,
                'contents_count' => $course->contents_count ?? 0,
            ]);

            $newCourse = $course->duplicate();

            \Log::info('Course duplicated successfully', [
                'original_course_id' => $course->id,
                'new_course_id' => $newCourse->id,
                'new_course_title' => $newCourse->title,
            ]);

            return redirect()->route('courses.index')
                ->with('success', "Course \"{$course->title}\" berhasil diduplikasi dengan {$course->lessons_count} lessons. Silakan tambahkan participants untuk kelas/batch baru.");
        } catch (\Illuminate\Database\QueryException $e) {
            // ✅ FIX: Handle database-specific errors
            \Log::error('Course duplication failed - Database Error', [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'sql' => $e->getSql() ?? 'N/A',
            ]);

            $errorMessage = 'Gagal menduplikasi course: Terjadi kesalahan database.';

            // Cek jika error karena duplicate key
            if (str_contains($e->getMessage(), 'Duplicate entry') ||
                str_contains($e->getMessage(), 'UNIQUE constraint')) {
                $errorMessage .= ' Kemungkinan ada data yang conflict (misal: enrollment token).';
            }

            return redirect()->route('courses.index')
                ->with('error', $errorMessage);
        } catch (\Exception $e) {
            // ✅ FIX: Log detailed error information
            \Log::error('Course duplication failed', [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // ✅ FIX: Return more detailed error message for debugging
            $errorMessage = 'Gagal menduplikasi course: ' . $e->getMessage();

            return redirect()->route('courses.index')
                ->with('error', $errorMessage);
        }
    }

    /**
     * Show token management page
     */
    public function tokens(Course $course)
    {
        $this->authorize('update', $course);

        $course->load(['classes.participants']);

        return view('courses.tokens', compact('course'));
    }

    /**
     * Generate enrollment token for course
     */
    public function generateToken(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'token_type' => 'required|in:random,custom',
            'custom_token' => 'required_if:token_type,custom|nullable|string|max:20',
            'token_length' => 'nullable|integer|min:4|max:20',
            'token_format' => 'nullable|in:alphanumeric,numeric,alpha'
        ]);

        $type = $request->token_type;
        $customToken = $request->custom_token;
        $length = $request->token_length ?? 8;
        $format = $request->token_format ?? 'alphanumeric';

        $result = $course->generateEnrollmentToken($type, $customToken, $length, $format);

        if ($result['success']) {
            return back()->with('success', "Token berhasil dibuat: {$result['token']}");
        } else {
            return back()->withErrors(['token' => $result['message']]);
        }
    }

    /**
     * Regenerate enrollment token for course
     */
    public function regenerateToken(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'token_type' => 'required|in:random,custom',
            'custom_token' => 'required_if:token_type,custom|nullable|string|max:20',
            'token_length' => 'nullable|integer|min:4|max:20',
            'token_format' => 'nullable|in:alphanumeric,numeric,alpha'
        ]);

        $type = $request->token_type;
        $customToken = $request->custom_token;
        $length = $request->token_length ?? 8;
        $format = $request->token_format ?? 'alphanumeric';

        $result = $course->generateEnrollmentToken($type, $customToken, $length, $format);

        if ($result['success']) {
            return back()->with('success', "Token baru berhasil dibuat: {$result['token']}");
        } else {
            return back()->withErrors(['token' => $result['message']]);
        }
    }

    /**
     * Toggle token enabled/disabled
     */
    public function toggleToken(Course $course)
    {
        $this->authorize('update', $course);

        try {
            $course->token_enabled = !$course->token_enabled;
            $course->save();

            $status = $course->token_enabled ? 'diaktifkan' : 'dinonaktifkan';

            return back()->with('success', "Token berhasil {$status}");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal toggle token: ' . $e->getMessage()]);
        }
    }
}
