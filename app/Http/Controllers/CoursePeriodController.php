<?php
// app/Http/Controllers/CoursePeriodController.php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoursePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CoursePeriodController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('super-admin')) {
            $periods = CoursePeriod::with('course')->latest()->paginate(15);
        } else {
            $periods = CoursePeriod::whereHas('course', function ($q) use ($user) {
                $q->whereHas('instructors', function ($sq) use ($user) {
                    $sq->where('user_id', $user->id);
                });
            })->with('course')->latest()->paginate(15);
        }

        return view('course-periods.index', compact('periods'));
    }

    public function create(Request $request)
    {
        $courseId = $request->get('course');
        $course = null;

        if ($courseId) {
            $course = Course::findOrFail($courseId);
            $this->authorize('update', $course);
        } else {
            $this->authorize('create', CoursePeriod::class);
        }

        $courses = Course::whereHas('instructors', function ($q) {
            $q->where('user_id', Auth::id());
        })->orWhere(function ($q) {
            if (Auth::user()->hasRole('super-admin')) {
                $q->whereRaw('1=1'); // Admin can see all courses
            }
        })->get();

        return view('course-periods.create', compact('courses', 'course'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'max_participants' => 'nullable|integer|min:1',
        ]);

        $course = Course::findOrFail($validatedData['course_id']);
        $this->authorize('update', $course);

        $startDate = Carbon::parse($validatedData['start_date']);
        $status = $startDate->isToday() || $startDate->isPast() ? 'active' : 'upcoming';

        $period = CoursePeriod::create([
            'course_id' => $validatedData['course_id'],
            'name' => $validatedData['name'],
            'start_date' => $startDate,
            'end_date' => Carbon::parse($validatedData['end_date']),
            'status' => $status,
            'description' => $validatedData['description'],
            'max_participants' => $validatedData['max_participants'],
        ]);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Periode kursus berhasil dibuat!');
    }

    public function show(CoursePeriod $coursePeriod)
    {
        $this->authorize('view', $coursePeriod);

        $coursePeriod->load(['course', 'chats.activeParticipants']);

        return view('course-periods.show', compact('coursePeriod'));
    }

    public function edit(CoursePeriod $coursePeriod)
    {
        $this->authorize('update', $coursePeriod);

        return view('course-periods.edit', compact('coursePeriod'));
    }

    public function update(Request $request, CoursePeriod $coursePeriod)
    {
        $this->authorize('update', $coursePeriod);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'max_participants' => 'nullable|integer|min:1',
        ]);

        $coursePeriod->update($validatedData);

        return redirect()->route('courses.show', $coursePeriod->course)
            ->with('success', 'Periode kursus berhasil diperbarui!');
    }

    public function destroy(CoursePeriod $coursePeriod)
    {
        $this->authorize('delete', $coursePeriod);

        $course = $coursePeriod->course;
        $coursePeriod->delete();

        return redirect()->route('courses.show', $course)
            ->with('success', 'Periode kursus berhasil dihapus!');
    }
}
