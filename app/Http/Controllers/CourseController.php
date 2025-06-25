<?php

namespace App\Http\Controllers;

use App\Models\Course; // Pastikan ini ada
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pastikan ini ada
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourseController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $courses = collect(); // Inisialisasi koleksi kosong

        if ($user->isAdmin()) {
            // Admin bisa melihat semua kursus
            $courses = Course::latest()->get();
        } elseif ($user->isInstructor()) {
            // Instruktur hanya bisa melihat kursus yang dia buat
            $courses = $user->courses()->latest()->get();
        } else {
            // Peserta tidak boleh mengakses halaman ini, harusnya dialihkan oleh middleware 'can'
            // Namun, sebagai fallback, jika role tidak cocok, kembalikan kosong.
            abort(403, 'Unauthorized action.'); // Atau redirect ke dashboard
        }

        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Untuk upload gambar
        ]);

        // Tangani upload gambar thumbnail
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // Buat kursus baru
        Course::create([
            'user_id' => Auth::id(), // ID instruktur yang login
            'title' => $request->title,
            'description' => $request->description,
            'objectives' => $request->objectives,
            'thumbnail' => $thumbnailPath,
            'status' => 'draft', // Default ke draft
        ]);

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        // Otorisasi: Semua user bisa melihat detail kursus (akan diperketat nanti untuk enrollment)
        // if (Auth::user()->isParticipant() && !$course->isEnrolled(Auth::id())) {
        //     abort(403, 'Anda belum terdaftar di kursus ini.');
        // }

        // Load pelajaran bersama dengan kontennya
        $course->load('lessons.contents');

        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        // Menggunakan Policy untuk otorisasi. Hanya admin atau pemilik kursus yang bisa edit.
        $this->authorize('update', $course);

        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        // Menggunakan Policy untuk otorisasi. Hanya admin atau pemilik kursus yang bisa update.
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Opsional, jika upload baru
            'status' => ['required', Rule::in(['draft', 'published'])], // Validasi untuk status
        ]);

        $data = $request->except(['_token', '_method', 'thumbnail']); // Ambil semua kecuali token, method, dan thumbnail

        // Tangani upload gambar thumbnail baru
        if ($request->hasFile('thumbnail')) {
            // Hapus thumbnail lama jika ada
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        } elseif ($request->input('clear_thumbnail')) {
            // Logika untuk menghapus thumbnail jika ada checkbox "Hapus Gambar"
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
                $data['thumbnail'] = null;
            }
        } else {
             // Jika tidak ada upload baru dan tidak ada perintah hapus, pertahankan yang lama
            $data['thumbnail'] = $course->thumbnail;
        }


        $course->update($data);

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        // Menggunakan Policy untuk otorisasi. Hanya admin atau pemilik kursus yang bisa hapus.
        $this->authorize('delete', $course);

        // Hapus thumbnail terkait jika ada
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        // Hapus kursus (ini juga akan menghapus pelajaran dan konten terkait karena onDelete('cascade'))
        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil dihapus!');
    }

    public function enrollParticipant(Request $request, Course $course)
    {
        // Hanya admin atau instruktur pemilik kursus yang bisa meng-enroll
        $this->authorize('update', $course);

        $request->validate([
            'user_id' => 'required|exists:users,id', // Pastikan user_id ada di tabel users
        ]);

        $user = User::find($request->user_id);

        // Cek apakah user sudah enroll kursus ini
        if ($course->participants->contains($user->id)) {
            return redirect()->back()->with('error', 'Peserta ini sudah terdaftar di kursus ini.');
        }

        // Tambahkan peserta ke kursus
        $course->participants()->attach($user->id); // Menggunakan attach untuk relasi many-to-many

        return redirect()->back()->with('success', $user->name . ' berhasil ditambahkan ke kursus.');
    }

    public function unenrollParticipant(Course $course, User $user)
    {
        // Hanya admin atau instruktur pemilik kursus yang bisa meng-unenroll
        $this->authorize('update', $course);

        // Cabut akses peserta dari kursus
        $course->participants()->detach($user->id); // Menggunakan detach

        return redirect()->back()->with('success', $user->name . ' berhasil dihapus dari kursus ini.');
    }
}