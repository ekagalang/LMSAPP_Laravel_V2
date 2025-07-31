<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoursePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CoursePeriodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new course period.
     */
    public function create(Course $course)
    {
        $this->authorize('update', $course);

        return view('courses-period.create', compact('course'));
    }

    /**
     * Store a newly created course period in storage.
     */
    public function store(Request $request, Course $course)
    {

        $this->authorize('update', $course);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            /* 'start_date' => 'required|date|after_or_equal:today' */
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string|max:1000',
            'max_participants' => 'nullable|integer|min:1|max:1000',
            'status' => 'required|in:upcoming,active,completed,cancelled',
        ]);

        try {
            DB::beginTransaction();

            // Auto-determine status based on dates if not explicitly set
            $startDate = Carbon::parse($validatedData['start_date']);
            $endDate = Carbon::parse($validatedData['end_date']);
            $now = now();

            if ($validatedData['status'] === 'upcoming' && $startDate->isPast()) {
                $validatedData['status'] = 'active';
            }

            $period = $course->periods()->create([
                'name' => $validatedData['name'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'description' => $validatedData['description'],
                'max_participants' => $validatedData['max_participants'],
                'status' => $validatedData['status'],
            ]);

            DB::commit();

            return redirect()
                ->route('courses.show', $course)
                ->with('success', 'Periode kursus berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal membuat periode: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified course period.
     */
    public function edit(Course $course, CoursePeriod $period)
    {
        $this->authorize('update', $course);

        // Ensure the period belongs to the course
        if ($period->course_id !== $course->id) {
            abort(404, 'Periode tidak ditemukan untuk kursus ini.');
        }

        return view('course-periods.edit', compact('course', 'period'));
    }

    /**
     * Update the specified course period in storage.
     */
    public function update(Request $request, Course $course, CoursePeriod $period)
    {
        $this->authorize('update', $course);

        // Ensure the period belongs to the course
        if ($period->course_id !== $course->id) {
            abort(404, 'Periode tidak ditemukan untuk kursus ini.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string|max:1000',
            'max_participants' => 'nullable|integer|min:1|max:1000',
            'status' => 'required|in:upcoming,active,completed,cancelled',
        ]);

        try {
            DB::beginTransaction();

            $startDate = Carbon::parse($validatedData['start_date']);
            $endDate = Carbon::parse($validatedData['end_date']);

            $period->update([
                'name' => $validatedData['name'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'description' => $validatedData['description'],
                'max_participants' => $validatedData['max_participants'],
                'status' => $validatedData['status'],
            ]);

            DB::commit();

            return redirect()
                ->route('courses.show', $course)
                ->with('success', 'Periode kursus berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal memperbarui periode: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified course period from storage.
     */
    public function destroy(CoursePeriod $period, Course $course)
{
    $this->authorize('update', $course);

    // Ensure the period belongs to the course
    if ($period->course_id !== $course->id) {
        abort(404, 'Periode tidak ditemukan untuk kursus ini.');
    }

    try {
        DB::beginTransaction();

        // Check if period has enrolled participants or chats
        $enrolledCount = $period->enrolledUsers()->count();
        $chatsCount = $period->chats()->count();

        if ($enrolledCount > 0) {
            return back()->withErrors([
                'error' => "Tidak dapat menghapus periode yang memiliki {$enrolledCount} peserta terdaftar. Hapus atau pindahkan peserta terlebih dahulu."
            ]);
        }

        if ($chatsCount > 0) {
            return back()->withErrors([
                'error' => "Tidak dapat menghapus periode yang memiliki {$chatsCount} chat aktif. Hapus chat terlebih dahulu."
            ]);
        }

        $periodName = $period->name;
        $period->delete();

        DB::commit();

        return redirect()
            ->route('courses.show', $course)
            ->with('success', "Periode '{$periodName}' berhasil dihapus!");
    } catch (\Exception $e) {
        DB::rollBack();

        return back()->withErrors([
            'error' => 'Gagal menghapus periode: ' . $e->getMessage()
        ]);
    }
}

    /**
     * Duplicate a course period
     */
    public function duplicate(Course $course, CoursePeriod $period)
    {
        $this->authorize('update', $course);

        // Ensure the period belongs to the course
        if ($period->course_id !== $course->id) {
            abort(404, 'Periode tidak ditemukan untuk kursus ini.');
        }

        try {
            DB::beginTransaction();

            $newPeriod = $course->periods()->create([
                'name' => $period->name . ' (Copy)',
                'start_date' => $period->start_date,
                'end_date' => $period->end_date,
                'description' => $period->description,
                'max_participants' => $period->max_participants,
                'status' => 'upcoming', // Always set new copy as upcoming
            ]);

            DB::commit();

            return redirect()
                ->route('course-periods.edit', [$course, $newPeriod])
                ->with('success', 'Periode berhasil diduplikasi! Silakan edit detail periode baru.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Gagal menduplikasi periode: ' . $e->getMessage()
            ]);
        }
    }
}
