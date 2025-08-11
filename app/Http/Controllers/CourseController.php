<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\CoursePeriod;
use App\Models\CertificateTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use PDF;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('show');
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

    public function store(Request $request)
    {
        $this->authorize('create', Course::class);

        // Enhanced validation including periods
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',

            'enable_periods' => 'nullable|boolean',

            /* // ✅ UPDATED: Use 'required_with' to ensure start_date and end_date are only required if periods are provided
                ✅ UPDATED: Use 'after_or_equal:today' to ensure start_date is not in the past
                ✅ UPDATED: Use 'after' to ensure end_date is after start_date
                ✅ UPDATED: Use 'required_with' to ensure these fields are only validated if */
            // ✅ UPDATED: Optional periods validation
            'periods' => 'nullable|array',
            'periods.*.name' => 'required_with:periods|string|max:255',
            'periods.*.start_date' => 'required_with:periods|date|after_or_equal:today',
            // 'periods.*.start_date' => 'required_with:periods|date',
            'periods.*.end_date' => 'required_with:periods|date|after:periods.*.start_date',
            'periods.*.description' => 'nullable|string',
            'periods.*.max_participants' => 'nullable|integer|min:1',

            // ✅ UPDATED: Default period optional
            'create_default_period' => 'nullable|boolean',
            'default_start_date' => 'required_if:create_default_period,true|nullable|date|after_or_equal:today',
            'default_end_date' => 'required_if:create_default_period,true|nullable|date|after:default_start_date',
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
            ]);

            // Assign creator as instructor
            $course->instructors()->attach(Auth::id());

            if ($request->boolean('enable_periods')) {
                // Hanya buat periode jika fitur periode diaktifkan
                if (
                    $request->boolean('create_default_period') ||
                    (isset($validatedData['periods']) && is_array($validatedData['periods']) && count($validatedData['periods']) > 0)
                ) {
                    $this->createCoursePeriods($course, $request, $validatedData);
                }
                // Jika enable_periods true tapi tidak ada periode yang dibuat,
                // kita bisa biarkan kosong atau buat default, tergantung requirement
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

    /**
     * Handle course periods creation
     */
    private function createCoursePeriods(Course $course, Request $request, array $validatedData)
    {
        // Option 1: Create default period
        if ($request->boolean('create_default_period')) {
            $startDate = Carbon::parse($validatedData['default_start_date']);
            $course->periods()->create([
                'name' => $course->title . ' - Periode 1',
                'start_date' => $startDate,
                'end_date' => Carbon::parse($validatedData['default_end_date']),
                'status' => $startDate->isToday() || $startDate->isPast() ? 'active' : 'upcoming',
            ]);
        }

        // Option 2: Create custom periods
        if (isset($validatedData['periods']) && is_array($validatedData['periods']) && count($validatedData['periods']) > 0) {
            foreach ($validatedData['periods'] as $periodData) {
                $startDate = Carbon::parse($periodData['start_date']);
                $course->periods()->create([
                    'name' => $periodData['name'],
                    'start_date' => $startDate,
                    'end_date' => Carbon::parse($periodData['end_date']),
                    'status' => $startDate->isToday() || $startDate->isPast() ? 'active' : 'upcoming',
                    'description' => $periodData['description'] ?? null,
                    'max_participants' => $periodData['max_participants'] ?? null,
                ]);
            }
        }

        // Fallback: Create default open period if none created
        // if ($course->periods()->count() === 0) {
        //     $course->periods()->create([
        //         'name' => $course->title . ' - Default Period',
        //         'start_date' => now(),
        //         'end_date' => now()->addYear(),
        //         'status' => 'active',
        //     ]);
        // }
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
                        return redirect()->route('contents.show', $firstContent->id);
                    }
                }
            }
        }

        // Jika bukan peserta, atau jika tidak ada content, atau jika tidak terdaftar,
        // tampilkan halaman detail seperti biasa.
        $course->load('lessons.contents', 'instructors', 'enrolledUsers');

        $availableInstructors = User::role('instructor')
            ->whereNotIn('id', $course->instructors->pluck('id'))
            ->get();

        $unEnrolledParticipants = User::role('participant')
            ->whereDoesntHave('courses', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->orderBy('name')
            ->get();

        $availableOrganizers = User::role('event-organizer')
            ->whereNotIn('id', $course->eventOrganizers->pluck('id'))
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
            'certificate_template_id' => 'nullable|exists:certificate_templates,id', // <-- VALIDASI UNTUK TEMPLATE

            // ... (validasi periode tetap sama)
            'enable_periods' => 'nullable|boolean',
            'periods_to_delete' => 'nullable|array',
            'periods_to_delete.*' => 'exists:course_periods,id',
            'periods' => 'nullable|array',
            'periods.*.id' => 'nullable|exists:course_periods,id',
            'periods.*.name' => 'required_with:periods|string|max:255',
            'periods.*.start_date' => 'required_with:periods|date',
            'periods.*.end_date' => 'required_with:periods|date|after:periods.*.start_date',
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

    public function showProgress(Request $request, Course $course)
    {
        $this->authorize('viewProgress', $course);
        $user = Auth::user();

        $instructorCourses = Course::query()
            ->whereHas('instructors', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('title')
            ->get();

        $enrolledUsersQuery = $course->enrolledUsers();

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $enrolledUsersQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        $enrolledUsers = $enrolledUsersQuery->get();

        // ✅ PERBAIKAN: Gunakan method getProgressForCourse dari model User untuk setiap peserta
        $participantsProgress = $enrolledUsers->map(function ($participant) use ($course) {
            // Panggil fungsi yang sudah kita perbaiki di model User
            $progressData = $participant->getProgressForCourse($course);

            // Logika untuk 'last_position' masih sama, namun ini mungkin tidak 100% akurat
            // karena hanya memeriksa konten biasa. Untuk saat ini kita biarkan.
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

        $course->load('lessons.contents', 'enrolledUsers.completedContents');
        $allContents = $course->lessons->flatMap(fn($l) => $l->contents);
        $totalContentCount = $allContents->count();
        $allContentIds = $allContents->pluck('id');

        $participantsProgress = $course->enrolledUsers->map(function ($participant) use ($allContentIds, $totalContentCount) {
            $completedCount = $participant->completedContents->whereIn('id', $allContentIds)->count();
            $progressPercentage = $totalContentCount > 0 ? round(($completedCount / $totalContentCount) * 100) : 0;

            return [
                'name' => $participant->name,
                'email' => $participant->email,
                'progress_percentage' => $progressPercentage,
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
}
