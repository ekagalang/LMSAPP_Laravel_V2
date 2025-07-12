<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Course;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function index(Course $course)
    {
        // Otorisasi: Pastikan user adalah instruktur/admin untuk kursus ini.
        // Kita bisa gunakan policy 'update' dari CoursePolicy sebagai acuannya.
        $this->authorize('update', $course);

        // Ambil semua ID konten yang ada di dalam kursus ini
        $contentIds = $course->lessons()->with('contents')->get()->pluck('contents.*.id')->flatten();

        // Ambil semua diskusi yang terkait dengan konten-konten tersebut
        $discussions = Discussion::whereIn('content_id', $contentIds)
                                ->with(['user', 'replies', 'content.lesson']) // Muat relasi yang dibutuhkan
                                ->latest() // Urutkan dari yang terbaru
                                ->paginate(15); // Gunakan paginasi

        return view('discussions.index', compact('course', 'discussions'));
    }
    
    // Method untuk menyimpan topik diskusi baru
    public function store(Request $request, Content $content)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $content->discussions()->create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return back()->with('success', 'Topik diskusi berhasil dimulai!');
    }

    // Method untuk menyimpan balasan baru
    public function storeReply(Request $request, Discussion $discussion)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $discussion->replies()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        return back()->with('success', 'Balasan berhasil dikirim!');
    }
}