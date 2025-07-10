<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Course; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LessonController extends Controller
{
    use AuthorizesRequests;
    /**
     * Show the form for creating a new lesson for a specific course.
     */
    public function create(Course $course)
    {
        // Pastikan user punya izin untuk mengelola kursus ini
        $this->authorize('update', $course); // Memastikan user bisa update kursus (pemilik atau admin)
        return view('lessons.create', compact('course'));
    }

    /**
     * Store a newly created lesson in storage.
     */
    public function store(Request $request, Course $course)
    {
        $this->authorize('update', $course); // Memastikan user bisa update kursus (pemilik atau admin)

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $course->lessons()->create([
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order ?? $course->lessons()->count() + 1, // Set order otomatis
        ]);

        return redirect()->route('courses.show', $course)->with('success', 'Pelajaran berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified lesson.
     */
    public function edit(Course $course, Lesson $lesson)
    {
        $this->authorize('update', $course); // Otorisasi berdasarkan kursus induk
        return view('lessons.edit', compact('course', 'lesson'));
    }

    /**
     * Update the specified lesson in storage.
     */
    public function update(Request $request, Course $course, Lesson $lesson)
    {
        $this->authorize('update', $course); // Otorisasi berdasarkan kursus induk

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $lesson->update([
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order ?? $lesson->order,
        ]);

        return redirect()->route('courses.show', $course)->with('success', 'Pelajaran berhasil diperbarui!');
    }

    /**
     * Remove the specified lesson from storage.
     */
    public function destroy(Course $course, Lesson $lesson)
    {
        $this->authorize('update', $course); // Otorisasi berdasarkan kursus induk
        $lesson->delete();
        return redirect()->route('courses.show', $course)->with('success', 'Pelajaran berhasil dihapus!');
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'lessons' => 'required|array',
            'lessons.*' => 'integer|exists:lessons,id',
        ]);

        // Ambil pelajaran pertama untuk otorisasi
        $firstLesson = Lesson::find($request->lessons[0]);
        if ($firstLesson) {
            $this->authorize('update', $firstLesson->course);
        }

        foreach ($request->lessons as $index => $lessonId) {
            Lesson::where('id', $lessonId)->update(['order' => $index + 1]);
        }

        return response()->json(['status' => 'success', 'message' => 'Urutan pelajaran berhasil diperbarui.']);
    }
}