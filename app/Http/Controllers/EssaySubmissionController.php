<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\EssaySubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EssaySubmissionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Content $content)
    {
        // Pastikan konten adalah esai
        if ($content->type !== 'essay') {
            return back()->with('error', 'Invalid content type.');
        }

        // Validasi request
        $request->validate([
            'essay_content' => 'required|string',
        ]);

        $user = Auth::user();

        // Menggunakan updateOrCreate untuk menangani jika user mengirim ulang jawaban
        EssaySubmission::updateOrCreate(
            [
                'user_id' => $user->id,
                'content_id' => $content->id,
            ],
            [
                // Sesuaikan nama kolom 'answer' dengan yang ada di database Anda
                'answer' => $request->input('essay_content'),
                'status' => 'submitted', // Tambahkan status
                'score' => null, // Reset skor jika ada pengiriman ulang
                'feedback' => null, // Reset feedback jika ada pengiriman ulang
            ]
        );

        // =================================================================
        // PERUBAHAN UTAMA: Tandai konten sebagai 'completed' untuk pengguna
        // =================================================================
        $user->completedContents()->syncWithoutDetaching([
            $content->id => ['completed' => true, 'completed_at' => now()]
        ]);


        // Cek apakah lesson (materi) sekarang sudah selesai
        $lesson = $content->lesson;
        if ($lesson) {
            $allContentsCompleted = $user->hasCompletedAllContentsInLesson($lesson);
            if ($allContentsCompleted) {
                $user->lessons()->syncWithoutDetaching([$lesson->id => ['status' => 'completed']]);
            }
        }
        
        return redirect()->route('contents.show', $content)->with('success', 'Essay submitted successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function showResult(EssaySubmission $submission)
    {
        // Pastikan hanya pengguna yang membuat submission yang bisa melihat hasilnya.
        if (Auth::id() !== $submission->user_id) {
            abort(403, 'UNAUTHORIZED ACTION');
        }

        // Muat relasi yang diperlukan untuk ditampilkan di view
        $submission->load('content.lesson.course', 'user');

        // Tampilkan halaman hasil
        return view('essays.result', compact('submission'));
    }
}
