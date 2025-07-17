<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\EssaySubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EssaySubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Content $content)
    {
        // Pastikan konten adalah esai
        if ($content->type !== 'essay') {
            return back()->with('error', 'Invalid content type.');
        }

        // Validasi request, ubah 'answer' menjadi 'essay_content'
        $request->validate([
            'essay_content' => 'required|string',
        ]);

        // Cek apakah user sudah pernah submit
        $existingSubmission = EssaySubmission::where('user_id', Auth::id())
            ->where('content_id', $content->id)
            ->exists();

        if ($existingSubmission) {
            return back()->with('error', 'You have already submitted your essay.');
        }

        // Simpan submission
        EssaySubmission::create([
            'user_id' => Auth::id(),
            'content_id' => $content->id,
            // Gunakan input 'essay_content'
            'answer' => $request->input('essay_content'),
        ]);

        return redirect()->route('contents.show', $content)->with('success', 'Essay submitted successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(EssaySubmission $essaySubmission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EssaySubmission $essaySubmission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EssaySubmission $essaySubmission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EssaySubmission $essaySubmission)
    {
        //
    }

    public function showResult(EssaySubmission $submission)
    {
        // Pastikan hanya pengguna yang membuat submission yang bisa melihat hasilnya.
        // Ini adalah langkah keamanan yang penting.
        if (Auth::id() !== $submission->user_id) {
            abort(403, 'UNAUTHORIZED ACTION');
        }

        // Muat relasi yang diperlukan untuk ditampilkan di view
        $submission->load('content.lesson.course', 'user');

        // Tampilkan halaman hasil
        return view('essays.result', compact('submission'));
    }
}
