<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GradebookController extends Controller
{
    /**
     * Menampilkan halaman gradebook untuk sebuah kursus.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\View\View
     */
    public function index(Course $course)
    {
        // Otorisasi: Hanya user yang bisa mengelola kursus ini yang boleh melihat gradebook.
        // Kita akan menggunakan permission 'manage own courses' yang dimiliki Instruktur.
        $this->authorize('update', $course);

        // Ambil semua kuis yang ada di dalam kursus ini.
        // Kita asumsikan relasinya: Course -> Lessons -> Quizzes
        $quizzes = $course->lessons()->with('quizzes')->get()->pluck('quizzes')->flatten();

        // Ambil semua peserta (users) yang terdaftar di kursus ini.
        // Kita juga memuat percobaan kuis (quizAttempts) mereka untuk kuis-kuis yang relevan.
        $participants = $course->enrolledUsers()->with(['quizAttempts' => function ($query) use ($quizzes) {
            $query->whereIn('quiz_id', $quizzes->pluck('id'));
        }])->get();

        return view('gradebook.index', compact('course', 'participants', 'quizzes'));
    }
}
