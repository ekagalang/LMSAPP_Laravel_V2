<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use PDF;

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
        
        $courses = $query->with('instructors')->latest()->get();

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $this->authorize('create', Course::class);
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Course::class);

        // Validasi yang bersih dan benar
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validatedData['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course = new Course($validatedData);
        $course->save();

        // Otomatis tugaskan pembuat (admin) sebagai instruktur awal
        $course->instructors()->attach(Auth::id());

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil dibuat.');
    }

    public function show(Course $course)
    {
        $this->authorize('view', $course);

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
            
        return view('courses.show', compact('course', 'availableInstructors', 'unEnrolledParticipants'));
    }

    public function edit(Course $course)
    {
        $this->authorize('update', $course);
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
            'clear_thumbnail' => 'nullable|boolean'
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validatedData['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        } elseif ($request->boolean('clear_thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validatedData['thumbnail'] = null;
        }

        $course->update($validatedData);

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil diperbarui.');
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

        // ✅ LOGIKA BARU: Ambil semua kursus yang diajar instruktur ini untuk dropdown
        $instructorCourses = Course::query()
            ->where('user_id', $user->id)
            ->orderBy('title')
            ->get();

        // Ambil query dasar untuk peserta yang terdaftar
        $enrolledUsersQuery = $course->enrolledUsers();

        // Terapkan filter pencarian jika ada
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $enrolledUsersQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // Ambil data peserta yang sudah difilter
        $enrolledUsers = $enrolledUsersQuery->with('completedContents.lesson')->get();

        // Ambil semua konten kursus
        $course->load('lessons.contents');
        $allContents = $course->lessons->flatMap(fn($l) => $l->contents);
        $totalContentCount = $allContents->count();
        $allContentIds = $allContents->pluck('id');

        // Proses data progres untuk peserta yang sudah difilter
        $participantsProgress = $enrolledUsers->map(function ($participant) use ($allContentIds, $totalContentCount) {
            $completedContents = $participant->completedContents->whereIn('id', $allContentIds);
            $completedCount = $completedContents->count();
            $progressPercentage = $totalContentCount > 0 ? round(($completedCount / $totalContentCount) * 100) : 0;
            $lastCompletedContent = $completedContents->sortByDesc('pivot.created_at')->first();
            $lastPosition = $lastCompletedContent ? $lastCompletedContent->lesson->title : 'Belum Memulai';

            return [
                'id' => $participant->id,
                'name' => $participant->name,
                'email' => $participant->email,
                'completed_count' => $completedCount,
                'progress_percentage' => $progressPercentage,
                'last_position' => $lastPosition,
            ];
        });

        return view('courses.progress', [
            'course' => $course,
            'instructorCourses' => $instructorCourses, // Kirim daftar kursus ke view
            'participantsProgress' => $participantsProgress,
            'totalContentCount' => $totalContentCount,
        ]);
    }

    public function showParticipantProgress(Course $course, User $user)
    {
        // Otorisasi, pastikan yang mengakses adalah instruktur/admin kursus ini
        $this->authorize('viewProgress', $course);

        // Pastikan user yang diminta memang terdaftar di kursus ini
        if (!$course->enrolledUsers()->where('user_id', $user->id)->exists()) {
            abort(404, 'Peserta tidak terdaftar pada kursus ini.');
        }

        // Ambil semua data yang diperlukan
        $course->load(['lessons.contents' => function ($query) {
            $query->orderBy('order');
        }]);

        // Buat 'peta' dari konten yang sudah diselesaikan peserta untuk pencarian cepat
        $completedContentsMap = $user->completedContents->keyBy('id');

        // Kirim semua data ke view baru
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
        // Filter hanya user dengan peran instruktur
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
        // Otorisasi, pastikan hanya user yang berhak yang bisa mengunduh
        $this->authorize('viewProgress', $course);

        // Mengambil data progres (logika yang sama dengan method showProgress)
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

        // Data yang akan dikirim ke view PDF
        $data = [
            'course' => $course,
            'participantsProgress' => $participantsProgress,
            'date' => date('d M Y')
        ];

        // Membuat PDF
        $pdf = PDF::loadView('reports.progress_pdf', $data);

        // Mengunduh PDF dengan nama file yang dinamis
        return $pdf->download('laporan-progres-' . Str::slug($course->title) . '.pdf');
    }
}
