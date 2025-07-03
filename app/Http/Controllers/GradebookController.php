<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GradebookController extends Controller
{
    public function index(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $quizzes = $course->lessons()->with('quizzes')->get()->pluck('quizzes')->flatten();
        $participantsQuery = $course->enrolledUsers();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $participantsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $participants = $participantsQuery->with(['quizAttempts' => function ($query) use ($quizzes) {
            $query->whereIn('quiz_id', $quizzes->pluck('id'));
        }])->get();

        return view('gradebook.index', compact('course', 'participants', 'quizzes'));
    }

    public function review(QuizAttempt $attempt)
    {
        $attempt->load('user', 'quiz.questions.options', 'answers');
        $this->authorize('update', $attempt->quiz->lesson->course);
        return view('gradebook.review', compact('attempt'));
    }

    /**
     * PERUBAHAN: Menampilkan halaman feedback terpusat untuk satu peserta.
     */
    public function feedback(Course $course, User $user)
    {
        $this->authorize('update', $course);

        // Ambil semua kuis dalam kursus ini
        $quizzes = $course->lessons()->with('quizzes')->get()->pluck('quizzes')->flatten();

        // Ambil semua percobaan kuis oleh user ini untuk kuis-kuis tersebut
        $attempts = $user->quizAttempts()
            ->whereIn('quiz_id', $quizzes->pluck('id'))
            ->with('quiz')
            ->get();

        // **TAMBAHKAN INI:** Ambil feedback yang sudah ada dari pivot table
        $existingFeedback = $course->enrolledUsers()->where('user_id', $user->id)->first()->pivot->feedback ?? null;

        return view('gradebook.feedback', compact('course', 'user', 'quizzes', 'attempts', 'existingFeedback'));
    }
    
    public function storeFeedback(Request $request, Course $course, User $user)
    {
        $this->authorize('update', $course);

        $request->validate([
            'feedback' => 'nullable|string',
        ]);

        // Simpan feedback ke pivot table course_user
        DB::table('course_user')
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->update(['feedback' => $request->input('feedback')]);

        return redirect()->back()->with('success', 'Feedback berhasil disimpan.');
    }
}
