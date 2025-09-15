<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoursePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JoinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show unified join form for both course and period tokens
     */
    public function showJoinForm()
    {
        return view('join.form');
    }

    /**
     * Process token join - handles both course and period tokens
     */
    public function join(Request $request)
    {
        $request->validate([
            'token' => 'required|string|max:50',
        ], [
            'token.required' => 'Token bergabung wajib diisi',
            'token.string' => 'Token harus berupa teks',
            'token.max' => 'Token terlalu panjang',
        ]);

        $token = strtoupper(trim($request->token));
        $user = Auth::user();

        // First, try to find a course with this token
        $course = Course::findByToken($token);
        if ($course) {
            return $this->joinCourse($course, $user);
        }

        // If not found in courses, try to find a period with this token
        $period = CoursePeriod::findByToken($token);
        if ($period) {
            return $this->joinPeriod($period, $user);
        }

        // Token not found in either table
        return back()->withInput()->withErrors([
            'token' => 'Token tidak valid atau tidak ditemukan'
        ]);
    }

    /**
     * Join by direct token URL - handles both course and period tokens
     */
    public function joinByToken($token)
    {
        $token = strtoupper(trim($token));

        // Try to find course first
        $course = Course::with(['participants', 'instructors', 'lessons'])->where('join_token', $token)->first();
        if ($course) {
            return view('join.confirm-course', compact('course', 'token'));
        }

        // Try to find period
        $period = CoursePeriod::findByToken($token);
        if ($period) {
            return view('join.confirm-period', compact('period', 'token'));
        }

        // Token not found
        return redirect()->route('join.form')->withErrors([
            'token' => 'Token tidak valid atau tidak ditemukan'
        ])->withInput(['token' => $token]);
    }

    /**
     * Confirm join for course token
     */
    public function confirmJoinCourse(Request $request, $token)
    {
        $token = strtoupper(trim($token));
        $course = Course::findByToken($token);

        if (!$course) {
            return redirect()->route('join.form')->withErrors([
                'token' => 'Token kursus tidak valid atau tidak ditemukan'
            ]);
        }

        $user = Auth::user();
        return $this->joinCourse($course, $user);
    }

    /**
     * Confirm join for period token
     */
    public function confirmJoinPeriod(Request $request, $token)
    {
        $token = strtoupper(trim($token));
        $period = CoursePeriod::findByToken($token);

        if (!$period) {
            return redirect()->route('join.form')->withErrors([
                'token' => 'Token periode tidak valid atau tidak ditemukan'
            ]);
        }

        $user = Auth::user();
        return $this->joinPeriod($period, $user);
    }

    /**
     * Handle joining a course
     */
    private function joinCourse(Course $course, $user)
    {
        // Check if course can accept new members with token
        if (!$course->canJoinWithToken()) {
            return back()->withErrors([
                'token' => 'Kursus tidak tersedia untuk bergabung dengan token (status: ' . $course->status . ')'
            ]);
        }

        // Check if user is already enrolled in this course
        if ($course->participants()->where('users.id', $user->id)->exists()) {
            return back()->withErrors([
                'token' => 'Anda sudah terdaftar dalam kursus ini'
            ]);
        }

        try {
            DB::beginTransaction();

            // Join the course
            $course->participants()->attach($user->id, [
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return redirect()->route('courses.show', $course)
                ->with('success', "Selamat! Anda berhasil bergabung ke kursus '{$course->title}'!");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'token' => 'Terjadi kesalahan saat bergabung ke kursus. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Handle joining a course period
     */
    private function joinPeriod(CoursePeriod $period, $user)
    {
        // Check if user can join with token
        if (!$period->canJoinWithToken($user->id)) {
            $errors = [];

            if ($period->isCompleted()) {
                $errors['token'] = 'Periode sudah selesai, tidak bisa bergabung lagi';
            } elseif (!$period->hasAvailableSlots()) {
                $errors['token'] = 'Periode sudah penuh, tidak ada slot tersedia';
            } elseif ($period->isParticipant($user->id)) {
                $errors['token'] = 'Anda sudah terdaftar dalam periode ini';
            } else {
                $errors['token'] = 'Tidak dapat bergabung ke periode ini';
            }

            return back()->withErrors($errors);
        }

        try {
            DB::beginTransaction();

            // First, make sure user is enrolled in the course
            $course = $period->course;
            if (!$course->participants()->where('users.id', $user->id)->exists()) {
                $course->participants()->attach($user->id, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Then, join the period
            $period->participants()->attach($user->id, [
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return redirect()->route('courses.show', $course)
                ->with('success', "Selamat! Anda berhasil bergabung ke periode '{$period->name}' untuk kursus '{$course->title}'!");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'token' => 'Terjadi kesalahan saat bergabung ke periode. Silakan coba lagi.'
            ]);
        }
    }
}