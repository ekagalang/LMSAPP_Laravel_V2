<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\Quiz;       // <--- Tambahkan ini
use App\Models\Question;  // <--- Tambahkan ini
use App\Models\Option;    // <--- Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str; // <--- Tambahkan ini untuk Str::lower
class ContentController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display the specified content (for participants).
     */
    public function show(Lesson $lesson, Content $content)
    {
        // Nanti di sini akan ada logic untuk memastikan user sudah enroll kursus.
        // Untuk saat ini, kita tampilkan saja.
        $course = $lesson->course; // Ambil kursus induk

        // Otorisasi sederhana: user harus sudah login untuk melihat konten
        // if (!Auth::check()) {
        //     return redirect()->route('login')->with('info', 'Silakan login untuk melihat konten.');
        // }

        // Anda bisa tambahkan otorisasi lebih lanjut di sini, misal cek enrollment
        // if (Auth::user()->isParticipant() && !$course->isEnrolled(Auth::id())) {
        //     abort(403, 'Anda tidak memiliki akses ke konten ini.');
        // }

        return view('contents.show', compact('lesson', 'content', 'course'));
    }

    /**
     * Show the form for creating a new content for a specific lesson.
     */
    public function create(Lesson $lesson)
    {
        // Otorisasi: Pastikan user bisa mengelola kursus induk dari pelajaran ini
        $this->authorize('update', $lesson->course);
        return view('contents.create', compact('lesson'));
    }

    /**
     * Store a newly created content in storage.
     */
    public function store(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson->course);

        $rules = [
            'title' => 'required|string|max:255',
            'type' => ['required', Rule::in(['text', 'video', 'document', 'image', 'quiz'])], // PASTIKAN 'quiz' ADA DI SINI
            'order' => 'nullable|integer',
        ];

        $filePath = null;
        $bodyContent = $request->body;

        if ($request->hasFile('file_upload')) {
            $filePath = $request->file('file_upload')->store('content_files', 'public');
            // Jika ada file upload, body bisa kosong atau berisi nama file, tergantung kebutuhan
            $bodyContent = null; // Atau $request->file_upload->getClientOriginalName();
        }

        $lesson->contents()->create([
            'title' => $request->title,
            'type' => $request->type,
            'body' => $bodyContent,
            'file_path' => $filePath,
            'order' => $request->order ?? $lesson->contents()->count() + 1,
        ]);

        return redirect()->route('courses.show', $lesson->course)->with('success', 'Konten berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified content.
     */
    public function edit(Lesson $lesson, Content $content)
    {
        $this->authorize('update', $lesson->course);
        return view('contents.edit', compact('lesson', 'content'));
    }

    /**
     * Update the specified content in storage.
     */
    public function update(Request $request, Lesson $lesson, Content $content)
    {
        $this->authorize('update', $lesson->course);

        $rules = [
            'title' => 'required|string|max:255',
            'type' => ['required', Rule::in(['text', 'video', 'document', 'image', 'quiz'])], // PASTIKAN 'quiz' ADA DI SINI
            'order' => 'nullable|integer',
        ];

        $filePath = $content->file_path;
        $bodyContent = $request->body;

        if ($request->hasFile('file_upload')) {
            // Hapus file lama jika ada
            if ($content->file_path) {
                Storage::disk('public')->delete($content->file_path);
            }
            $filePath = $request->file('file_upload')->store('content_files', 'public');
            $bodyContent = null;
        } elseif ($request->type !== 'text' && !$request->body && !$request->file_upload && $content->file_path) {
            // Jika tipe bukan teks, dan tidak ada body/file baru, tapi ada file lama
            // Ini untuk kasus user memilih tipe file tapi tidak upload file baru.
            // Jika file lama ingin dipertahankan, tidak perlu hapus
            // Jika ingin menghapus, ini akan jadi sedikit lebih kompleks
        }


        $content->update([
            'title' => $request->title,
            'type' => $request->type,
            'body' => $bodyContent,
            'file_path' => $filePath,
            'order' => $request->order ?? $content->order,
        ]);

        return redirect()->route('courses.show', $lesson->course)->with('success', 'Konten berhasil diperbarui!');
    }

    /**
     * Remove the specified content from storage.
     */
    public function destroy(Lesson $lesson, Content $content)
    {
        $this->authorize('update', $lesson->course);

        // Hapus file terkait jika ada
        if ($content->file_path) {
            Storage::disk('public')->delete($content->file_path);
        }

        $content->delete();
        return redirect()->route('courses.show', $lesson->course)->with('success', 'Konten berhasil dihapus!');
    }
}