<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CourseClassController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of course periods.
     */
    public function index(Course $course)
    {
        $this->authorize('view', $course);
        
        $periods = $course->periods()
            ->with(['instructors', 'participants', 'chats'])
            ->orderBy('start_date', 'desc')
            ->paginate(12);

        return view('course-periods.index', compact('course', 'periods'));
    }

    /**
     * Display the specified course period.
     */
    public function show(Course $course, CourseClass $period)
    {
        $this->authorize('view', $course);

        // Ensure the period belongs to the course
        if ($period->course_id !== $course->id) {
            abort(404, 'Periode tidak ditemukan untuk kursus ini.');
        }

        $period->load(['instructors', 'participants', 'chats']);

        return view('course-periods.show', compact('course', 'period'));
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
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string|max:1000',
            'max_participants' => 'nullable|integer|min:1|max:1000',
            'status' => 'required|in:upcoming,active,completed,cancelled',
        ]);

        try {
            DB::beginTransaction();

            // Handle optional dates
            $startDate = isset($validatedData['start_date']) ? Carbon::parse($validatedData['start_date']) : null;
            $endDate = isset($validatedData['end_date']) ? Carbon::parse($validatedData['end_date']) : null;

            // Auto-determine status based on dates if not explicitly set
            if ($startDate && $validatedData['status'] === 'upcoming' && $startDate->isPast()) {
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
    public function edit(Course $course, CourseClass $period)
    {
        $this->authorize('update', $course);

        // Ensure the period belongs to the course
        if ($period->course_id !== $course->id) {
            abort(404, 'Periode tidak ditemukan untuk kursus ini.');
        }

        return view('courses-period.edit', compact('course', 'period'));
    }

    /**
     * Update the specified course period in storage.
     */
    public function update(Request $request, Course $course, CourseClass $period)
    {
        $this->authorize('update', $course);

        // Ensure the period belongs to the course
        if ($period->course_id !== $course->id) {
            abort(404, 'Periode tidak ditemukan untuk kursus ini.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string|max:1000',
            'max_participants' => 'nullable|integer|min:1|max:1000',
            'status' => 'required|in:upcoming,active,completed,cancelled',
        ]);

        try {
            DB::beginTransaction();

            $startDate = isset($validatedData['start_date']) ? Carbon::parse($validatedData['start_date']) : null;
            $endDate = isset($validatedData['end_date']) ? Carbon::parse($validatedData['end_date']) : null;

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
    public function destroy(Course $course, CourseClass $period)
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
    public function duplicate(Course $course, CourseClass $period)
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

    /**
     * Show period management page (instructors and participants)
     */
    public function manage(Course $course, CourseClass $period)
    {
        $this->authorize('update', $course);

        if ($period->course_id !== $course->id) {
            abort(404, 'Periode tidak ditemukan untuk kursus ini.');
        }

        $period->load(['instructors', 'participants']);

        // Get instructors who are already assigned to ANY class of this course
        $assignedInstructorIds = DB::table('course_class_instructor')
            ->join('course_classes', 'course_class_instructor.course_class_id', '=', 'course_classes.id')
            ->where('course_classes.course_id', $course->id)
            ->pluck('course_class_instructor.user_id')
            ->unique();

        // Get available instructors (from course instructors, excluding those assigned to any class)
        $availableInstructors = $course->instructors()
            ->whereNotIn('users.id', $assignedInstructorIds)
            ->get();

        // Get participants who are already assigned to ANY class of this course
        $assignedParticipantIds = DB::table('course_class_user')
            ->join('course_classes', 'course_class_user.course_class_id', '=', 'course_classes.id')
            ->where('course_classes.course_id', $course->id)
            ->pluck('course_class_user.user_id')
            ->unique();

        // Get available participants (from course participants, excluding those assigned to any class)
        $availableParticipants = $course->participants()
            ->whereNotIn('users.id', $assignedParticipantIds)
            ->get();

        return view('course-periods.manage', compact(
            'course', 
            'period', 
            'availableInstructors', 
            'availableParticipants'
        ));
    }

    /**
     * Add instructor to period
     */
    public function addInstructor(Request $request, Course $course, CourseClass $period)
    {
        $this->authorize('update', $course);

        if ($period->course_id !== $course->id) {
            abort(404);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);

        // Check if user is course instructor
        if (!$course->instructors()->where('users.id', $user->id)->exists()) {
            return back()->withErrors([
                'error' => 'User harus menjadi instructor dari course ini terlebih dahulu.'
            ]);
        }

        // Check if already assigned
        if ($period->instructors()->where('users.id', $user->id)->exists()) {
            return back()->withErrors([
                'error' => 'Instructor sudah terdaftar di periode ini.'
            ]);
        }

        $period->instructors()->attach($user->id);

        return back()->with('success', "Instructor {$user->name} berhasil ditambahkan ke periode {$period->name}.");
    }

    /**
     * Remove instructor from period
     */
    public function removeInstructor(Course $course, CourseClass $period, User $user)
    {
        $this->authorize('update', $course);

        if ($period->course_id !== $course->id) {
            abort(404);
        }

        $period->instructors()->detach($user->id);

        return back()->with('success', "Instructor {$user->name} berhasil dihapus dari periode {$period->name}.");
    }

    /**
     * Add participant(s) to period - supports multiple selection
     */
    public function addParticipant(Request $request, Course $course, CourseClass $period)
    {
        $this->authorize('update', $course);

        if ($period->course_id !== $course->id) {
            abort(404);
        }

        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        $userIds = $request->user_ids;
        $addedCount = 0;
        $errors = [];

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            
            // Check if user is course participant
            if (!$course->participants()->where('users.id', $userId)->exists()) {
                $errors[] = "{$user->name} harus menjadi participant dari course ini terlebih dahulu.";
                continue;
            }

            // Check if already enrolled
            if ($period->participants()->where('users.id', $userId)->exists()) {
                $errors[] = "{$user->name} sudah terdaftar di periode ini.";
                continue;
            }

            // Check available slots
            if (!$period->hasAvailableSlots()) {
                $errors[] = "Periode sudah penuh. Maksimal {$period->max_participants} participants.";
                break;
            }

            $period->participants()->attach($userId);
            $addedCount++;
        }

        if ($addedCount > 0) {
            $message = "Berhasil menambahkan {$addedCount} participant ke periode {$period->name}.";
            if (count($errors) > 0) {
                $message .= " Beberapa participant tidak bisa ditambahkan: " . implode(', ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= " dan " . (count($errors) - 3) . " lainnya.";
                }
            }
            return back()->with('success', $message);
        }

        return back()->withErrors(['error' => implode(' ', $errors)]);
    }

    /**
     * Remove participant from period
     */
    public function removeParticipant(Course $course, CourseClass $period, User $user)
    {
        $this->authorize('update', $course);

        if ($period->course_id !== $course->id) {
            abort(404);
        }

        $period->participants()->detach($user->id);

        return back()->with('success', "Participant {$user->name} berhasil dihapus dari periode {$period->name}.");
    }

    /**
     * Bulk remove participants from period
     */
    public function bulkRemoveParticipants(Request $request, Course $course, CourseClass $period)
    {
        $this->authorize('update', $course);

        if ($period->course_id !== $course->id) {
            abort(404);
        }

        $request->validate([
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:users,id'
        ]);

        $participantIds = $request->participant_ids;
        $removedCount = 0;

        foreach ($participantIds as $participantId) {
            if ($period->participants()->where('users.id', $participantId)->exists()) {
                $period->participants()->detach($participantId);
                $removedCount++;
            }
        }

        return back()->with('success', "Berhasil menghapus {$removedCount} participant dari periode {$period->name}.");
    }

    /**
     * Enroll user to a specific period (for public enrollment)
     */
    public function enroll(Course $course, CourseClass $period)
    {
        $user = Auth::user();

        // Check if period is available for enrollment
        if (!in_array($period->status, ['active', 'upcoming'])) {
            return back()->withErrors([
                'error' => 'Periode tidak tersedia untuk pendaftaran.'
            ]);
        }

        // Check if user is already course participant
        if (!$course->participants()->where('users.id', $user->id)->exists()) {
            return back()->withErrors([
                'error' => 'Anda harus terdaftar di course ini untuk bergabung dengan periode.'
            ]);
        }

        // Check if already enrolled in this period
        if ($period->participants()->where('users.id', $user->id)->exists()) {
            return back()->withErrors([
                'error' => 'Anda sudah terdaftar di periode ini.'
            ]);
        }

        // Check available slots
        if (!$period->hasAvailableSlots()) {
            return back()->withErrors([
                'error' => 'Periode sudah penuh. Maksimal ' . $period->max_participants . ' participants.'
            ]);
        }

        $period->participants()->attach($user->id);

        return back()->with('success', "Berhasil bergabung dengan periode {$period->name}!");
    }

    /**
     * Generate enrollment token for class
     */
    public function generateToken(Request $request, Course $course, CourseClass $period)
    {
        $this->authorize('update', $course);

        if ($period->course_id !== $course->id) {
            abort(404);
        }

        $request->validate([
            'token_type' => 'required|in:random,custom',
            'custom_token' => 'required_if:token_type,custom|nullable|string|max:20',
            'token_length' => 'nullable|integer|min:4|max:20',
            'token_format' => 'nullable|in:alphanumeric,numeric,alpha'
        ]);

        $type = $request->token_type;
        $customToken = $request->custom_token;
        $length = $request->token_length ?? 8;
        $format = $request->token_format ?? 'alphanumeric';

        $result = $period->generateEnrollmentToken($type, $customToken, $length, $format);

        if ($result['success']) {
            return back()->with('success', "Token kelas berhasil dibuat: {$result['token']}");
        } else {
            return back()->withErrors(['token' => $result['message']]);
        }
    }

    /**
     * Regenerate enrollment token for class
     */
    public function regenerateToken(Request $request, Course $course, CourseClass $period)
    {
        $this->authorize('update', $course);

        if ($period->course_id !== $course->id) {
            abort(404);
        }

        $request->validate([
            'token_type' => 'required|in:random,custom',
            'custom_token' => 'required_if:token_type,custom|nullable|string|max:20',
            'token_length' => 'nullable|integer|min:4|max:20',
            'token_format' => 'nullable|in:alphanumeric,numeric,alpha'
        ]);

        $type = $request->token_type;
        $customToken = $request->custom_token;
        $length = $request->token_length ?? 8;
        $format = $request->token_format ?? 'alphanumeric';

        $result = $period->generateEnrollmentToken($type, $customToken, $length, $format);

        if ($result['success']) {
            return back()->with('success', "Token kelas baru berhasil dibuat: {$result['token']}");
        } else {
            return back()->withErrors(['token' => $result['message']]);
        }
    }

    /**
     * Toggle token enabled/disabled for class
     */
    public function toggleToken(Course $course, CourseClass $period)
    {
        $this->authorize('update', $course);

        if ($period->course_id !== $course->id) {
            abort(404);
        }

        try {
            $period->token_enabled = !$period->token_enabled;
            $period->save();

            $status = $period->token_enabled ? 'diaktifkan' : 'dinonaktifkan';

            return back()->with('success', "Token kelas berhasil {$status}");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal toggle token: ' . $e->getMessage()]);
        }
    }
}
