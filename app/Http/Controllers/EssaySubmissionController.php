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
        // Pastikan kontennya adalah tipe esai
        if ($content->type !== 'essay') {
            abort(404);
        }

        // Validasi input
        $request->validate([
            'answer' => 'required|string|min:20',
        ]);
        
        // Cek apakah user sudah pernah submit
        $existingSubmission = EssaySubmission::where('user_id', Auth::id())
                                              ->where('content_id', $content->id)
                                              ->exists();
        if ($existingSubmission) {
            return back()->with('error', 'Anda sudah pernah mengirimkan jawaban untuk tugas ini.');
        }

        // Simpan jawaban
        EssaySubmission::create([
            'user_id' => Auth::id(),
            'content_id' => $content->id,
            'answer' => $request->input('answer'),
        ]);

        return back()->with('success', 'Jawaban Anda berhasil dikirim!');
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
}
