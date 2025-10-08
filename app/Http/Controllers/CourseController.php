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

        if ($user->hasRole('instructor')) {
            $query->whereHas('instructors', function ($q) use ($user) {
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
        if ($user && $user->hasRole('participant')) {
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
        if ($user->hasRole('instructor') && !$user->hasRole(['super-admin', 'event-organizer'])) {
            // Replace the periods relation with filtered periods
            $course->setRelation('periods', $course->getUserInstructorPeriods($user->id));
        } else {
            // Load all periods for super-admin, event-organizer, or other roles
            $course->load('periods');
        }

        $availableInstructors = User::role('instructor')
            ->whereNotIn('id', $course->instructors->pluck('id'))
            ->orderBy('name')
            ->get();

        $unEnrolledParticipants = User::role('participant')
            ->whereDoesntHave('courses', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->orderBy('name')
            ->get();

        $availableOrganizers = User::role('event-organizer')
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
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }
        $course->delete();
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

        // Gunakan detach untuk menghapus hubungan antara kursus dan user
        $course->enrolledUsers()->detach($request->user_ids);

        return redirect()->back()->with('success', 'Akses peserta berhasil dicabut.');
    }

    public function showProgress(Course $course, Request $request)
    {
        $this->authorize('viewProgress', $course);

        $user = Auth::user();

        // ✅ FIX: Update query logic untuk course dropdown
        // Include courses untuk Event Organizers
        if ($user->hasRole('super-admin')) {
            // Super admin bisa lihat semua courses
            $instructorCourses = Course::with('instructors')->get();
        } elseif ($user->hasRole('event-organizer')) {
            // ✅ FIX: Event Organizer bisa lihat courses yang mereka kelola
            $instructorCourses = $user->eventOrganizedCourses()
                ->with('instructors')
                ->orderBy('title')
                ->get();
        } elseif ($user->hasRole('instructor')) {
            // Instructor hanya bisa lihat courses yang mereka ajar
            $instructorCourses = $user->taughtCourses()
                ->with('instructors')
                ->orderBy('title')
                ->get();
        } else {
            // Default: empty collection untuk roles lain
            $instructorCourses = collect();
        }

        // ✅ FIX: Ensure current course is included in dropdown
        if (!$instructorCourses->contains('id', $course->id)) {
            // If current course is not in the list, add it
            $instructorCourses->push($course);
        }

        // Filter participants by instructor's assigned periods
        if ($user->hasRole('instructor') && !$user->hasRole(['super-admin', 'event-organizer'])) {
            // Get periods where this instructor is assigned for this course
            $instructorPeriods = $user->instructorPeriods()
                ->where('course_id', $course->id)
                ->pluck('course_classes.id');
            
            if ($instructorPeriods->isNotEmpty()) {
                // Get participants only from instructor's assigned periods
                $enrolledUsersQuery = User::whereHas('participantPeriods', function ($query) use ($instructorPeriods) {
                    $query->whereIn('course_classes.id', $instructorPeriods);
                });
            } else {
                // If instructor is not assigned to any periods, show course-level participants
                $enrolledUsersQuery = $course->enrolledUsers();
            }
        } else {
            // For super-admin, event-organizer, or other roles, show all participants
            $enrolledUsersQuery = $course->enrolledUsers();
        }

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $enrolledUsersQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        $enrolledUsers = $enrolledUsersQuery->get();

        // Progress calculation tetap sama
        $participantsProgress = $enrolledUsers->map(function ($participant) use ($course) {
            $progressData = $participant->getProgressForCourse($course);

            $lastCompletedContent = $participant->completedContents
                ->where('lesson.course_id', $course->id)
                ->sortByDesc('pivot.completed_at')
                ->first();

            $lastPosition = $lastCompletedContent ? $lastCompletedContent->lesson->title : 'Belum Memulai';

            return [
                'id' => $participant->id,
                'name' => $participant->name,
                'email' => $participant->email,
                'completed_count' => $progressData['completed_contents'],
                'progress_percentage' => $progressData['progress_percentage'],
                'last_position' => $lastPosition,
            ];
        });

        $totalContentCount = $course->lessons()->withCount('contents')->get()->sum('contents_count');

        return view('courses.progress', [
            'course' => $course,
            'instructorCourses' => $instructorCourses,
            'participantsProgress' => $participantsProgress,
            'totalContentCount' => $totalContentCount,
        ]);
    }

    public function showParticipantProgress(Course $course, User $user)
    {
        $this->authorize('viewProgress', $course);

        if (!$course->enrolledUsers()->where('user_id', $user->id)->exists()) {
            abort(404, 'Peserta tidak terdaftar pada kursus ini.');
        }

        $course->load(['lessons.contents' => function ($query) {
            $query->orderBy('order');
        }]);

        // ✅ PERBAIKAN: Buat peta penyelesaian yang akurat untuk SEMUA jenis konten.
        // Kita akan iterasi semua konten dan menggunakan method hasCompletedContent dari model User.
        $completedContentsMap = collect();
        foreach ($course->lessons as $lesson) {
            foreach ($lesson->contents as $content) {
                // Gunakan method 'hasCompletedContent' dari model User yang sudah ada
                if ($user->hasCompletedContent($content)) {
                    $completedContentsMap->put($content->id, true);
                }
            }
        }

        return view('courses.participant_progress', [
            'course' => $course,
            'participant' => $user,
            'lessons' => $course->lessons,
            'completedContentsMap' => $completedContentsMap
        ]);
    }

    public function addInstructor(Request $request, Course $course)
    {
        $this->authorize('update', $course);
        $request->validate(['user_ids' => 'required|array']);
        $instructorIds = User::whereIn('id', $request->user_ids)->role('instructor')->pluck('id');
        $course->instructors()->syncWithoutDetaching($instructorIds);
        return back()->with('success', 'Instruktur berhasil ditambahkan.');
    }

    public function removeInstructor(Request $request, Course $course)
    {
        $this->authorize('update', $course);
        $request->validate(['user_ids' => 'required|array']);
        $course->instructors()->detach($request->user_ids);
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

        // Build course list for filter dropdown similar to showProgress
        if ($user->hasRole('super-admin')) {
            $courseOptions = Course::with('instructors')->orderBy('title')->get();
        } elseif ($user->hasRole('event-organizer')) {
            $courseOptions = $user->eventOrganizedCourses()->with('instructors')->orderBy('title')->get();
        } elseif ($user->hasRole('instructor')) {
            $courseOptions = $user->taughtCourses()->with('instructors')->orderBy('title')->get();
        } else {
            $courseOptions = collect();
        }
        if (!$courseOptions->contains('id', $course->id)) {
            $courseOptions->push($course);
        }

        // Filter participants by instructor periods (same logic as showProgress)
        if ($user->hasRole('instructor') && !$user->hasRole(['super-admin', 'event-organizer'])) {
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
                        $passMarks = (int) ($quiz->pass_marks ?? 0);
                        
                        foreach ($attempts as $index => $attempt) {
                            $totalMarks = (int) ($quiz->total_marks ?? 0);
                            if ($totalMarks <= 0) {
                                $totalMarks = $quiz->questions->sum('marks') ?: 1;
                            }
                            
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
        $course->eventOrganizers()->syncWithoutDetaching($organizerIds);
        return back()->with('success', 'Event Organizer berhasil ditambahkan.');
    }

    public function removeEventOrganizer(Request $request, Course $course)
    {
        $this->authorize('update', $course);
        $request->validate(['user_ids' => 'required|array']);
        $course->eventOrganizers()->detach($request->user_ids);
        return back()->with('success', 'Event Organizer berhasil dihapus.');
    }

    public function duplicate(Course $course)
    {
        // ✅ PERBAIKAN: Ganti otorisasi dari 'create' menjadi 'duplicate'
        $this->authorize('duplicate', Course::class);

        try {
            $newCourse = $course->duplicate();
            return redirect()->route('courses.index')
                ->with('success', "Course \"{$course->title}\" has been duplicated successfully.");
        } catch (\Exception $e) {
            return redirect()->route('courses.index')->with('error', 'Failed to duplicate course. Please try again.');
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
