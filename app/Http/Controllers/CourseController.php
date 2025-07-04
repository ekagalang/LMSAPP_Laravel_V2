<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('show');
    }

    public function index()
    {
        $this->authorize('viewAny', Course::class);

        $user = Auth::user();
        
        if ($user->hasRole('super-admin')) {
            $courses = Course::with('instructor')->latest()->get();
        } else {
            $courses = Course::where('user_id', $user->id)->with('instructor')->latest()->get();
        }

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

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $course = new Course($validatedData);
        $course->user_id = Auth::id();
        $course->save();

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil dibuat.');
    }

    public function show(Course $course)
    {
        // Ganti 'participants' menjadi 'enrolledUsers' agar sesuai dengan nama relasi di model Course
        $course->load('lessons.contents', 'instructor', 'enrolledUsers');
        return view('courses.show', compact('course'));
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
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $course->update($validatedData);

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);
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
}
