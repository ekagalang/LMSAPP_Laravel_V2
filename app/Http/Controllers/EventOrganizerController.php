<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class EventOrganizerController extends Controller
{
    /**
     * Menampilkan halaman daftar kursus untuk Event Organizer.
     */
    public function index()
    {
        // ✅ PERBAIKAN: Ganti 'instructor' menjadi 'instructors'
        $courses = Course::where('status', 'published')
            ->with('instructors') // <--- Perubahan di sini
            ->latest()
            ->get();
            
        return view('event_organizer.courses_index', compact('courses'));
    }
}