<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Content;
use App\Models\Course;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display attendance for a specific content (Zoom session)
     * With search and pagination support
     */
    public function index(Request $request, $contentId)
    {
        $content = Content::with('lesson.course')->findOrFail($contentId);

        // Only show attendance for content that requires it
        if (!$content->requiresAttendance()) {
            return redirect()->back()->with('error', 'This content does not require attendance tracking.');
        }

        $course = $content->lesson->course;

        // Get search query
        $search = $request->input('search');

        // Get all enrolled participants with search and pagination
        $participantsQuery = $course->participants()
            ->with(['attendances' => function($query) use ($contentId) {
                $query->where('content_id', $contentId);
            }]);

        // Apply search filter if provided
        if ($search) {
            $participantsQuery->where(function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Paginate participants (50 per page for 1000+ participants)
        $participants = $participantsQuery->paginate(50)->appends(['search' => $search]);

        // Get stats (from all participants, not just current page)
        $stats = $content->getAttendanceStats();

        return view('attendance.index', compact('content', 'course', 'participants', 'stats', 'search'));
    }

    /**
     * Mark attendance for a user
     */
    public function mark(Request $request, $contentId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:present,absent,late,excused',
            'duration_minutes' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
            'joined_at' => 'nullable|date',
            'left_at' => 'nullable|date|after:joined_at',
        ]);

        $content = Content::findOrFail($contentId);

        if (!$content->requiresAttendance()) {
            return response()->json([
                'success' => false,
                'message' => 'This content does not require attendance tracking.'
            ], 400);
        }

        $attendance = Attendance::markAttendance(
            $request->user_id,
            $contentId,
            $request->status,
            [
                'duration_minutes' => $request->duration_minutes ?? 0,
                'notes' => $request->notes,
                'joined_at' => $request->joined_at,
                'left_at' => $request->left_at,
                'marked_by' => auth()->user()->name,
            ]
        );

        // Log activity
        $user = User::find($request->user_id);
        ActivityLog::log('attendance_marked', [
            'description' => "Marked attendance for {$user->name} in {$content->title} as {$request->status}",
            'metadata' => [
                'participant_id' => $request->user_id,
                'participant_name' => $user->name,
                'content_id' => $contentId,
                'content_title' => $content->title,
                'status' => $request->status,
                'duration_minutes' => $request->duration_minutes ?? 0,
                'course_id' => $content->lesson->course_id ?? null,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance marked successfully',
            'attendance' => $attendance
        ]);
    }

    /**
     * Bulk mark attendance
     */
    public function bulkMark(Request $request, $contentId)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'status' => 'required|in:present,absent,late,excused',
            'duration_minutes' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $content = Content::findOrFail($contentId);

        if (!$content->requiresAttendance()) {
            return response()->json([
                'success' => false,
                'message' => 'This content does not require attendance tracking.'
            ], 400);
        }

        $marked = 0;
        $userNames = [];
        foreach ($request->user_ids as $userId) {
            Attendance::markAttendance(
                $userId,
                $contentId,
                $request->status,
                [
                    'duration_minutes' => $request->duration_minutes ?? 0,
                    'notes' => $request->notes,
                    'marked_by' => auth()->user()->name,
                ]
            );
            $user = User::find($userId);
            $userNames[] = $user->name;
            $marked++;
        }

        // Log bulk activity
        ActivityLog::log('attendance_bulk_marked', [
            'description' => "Bulk marked attendance for {$marked} participants in {$content->title} as {$request->status}",
            'metadata' => [
                'participant_count' => $marked,
                'participant_ids' => $request->user_ids,
                'participant_names' => $userNames,
                'content_id' => $contentId,
                'content_title' => $content->title,
                'status' => $request->status,
                'duration_minutes' => $request->duration_minutes ?? 0,
                'course_id' => $content->lesson->course_id ?? null,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => "Successfully marked attendance for {$marked} participant(s)",
            'marked' => $marked
        ]);
    }

    /**
     * Update attendance
     */
    public function update(Request $request, $attendanceId)
    {
        $request->validate([
            'status' => 'required|in:present,absent,late,excused',
            'duration_minutes' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
            'joined_at' => 'nullable|date',
            'left_at' => 'nullable|date|after:joined_at',
        ]);

        $attendance = Attendance::findOrFail($attendanceId);
        $oldStatus = $attendance->status;
        $oldDuration = $attendance->duration_minutes;

        $attendance->update([
            'status' => $request->status,
            'duration_minutes' => $request->duration_minutes ?? $attendance->duration_minutes,
            'notes' => $request->notes,
            'joined_at' => $request->joined_at ?? $attendance->joined_at,
            'left_at' => $request->left_at ?? $attendance->left_at,
            'marked_by' => auth()->user()->name,
        ]);

        // Log activity
        ActivityLog::log('attendance_updated', [
            'description' => "Updated attendance for {$attendance->user->name} in {$attendance->content->title}",
            'metadata' => [
                'attendance_id' => $attendanceId,
                'participant_name' => $attendance->user->name,
                'content_title' => $attendance->content->title,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'old_duration' => $oldDuration,
                'new_duration' => $attendance->duration_minutes,
                'course_id' => $attendance->course_id,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully',
            'attendance' => $attendance
        ]);
    }

    /**
     * Delete attendance record
     */
    public function destroy($attendanceId)
    {
        $attendance = Attendance::findOrFail($attendanceId);

        // Log before delete
        ActivityLog::log('attendance_deleted', [
            'description' => "Deleted attendance record for {$attendance->user->name} in {$attendance->content->title}",
            'metadata' => [
                'attendance_id' => $attendanceId,
                'participant_name' => $attendance->user->name,
                'content_title' => $attendance->content->title,
                'status' => $attendance->status,
                'duration_minutes' => $attendance->duration_minutes,
                'course_id' => $attendance->course_id,
            ]
        ]);

        $attendance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attendance record deleted successfully'
        ]);
    }

    /**
     * Export attendance to CSV
     */
    public function export($contentId)
    {
        $content = Content::with('lesson.course')->findOrFail($contentId);
        $course = $content->lesson->course;

        $participants = $course->participants()->with(['attendances' => function($query) use ($contentId) {
            $query->where('content_id', $contentId);
        }])->get();

        $filename = 'attendance_' . str_replace(' ', '_', $content->title) . '_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($participants, $content) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Status', 'Duration (minutes)',
                'Joined At', 'Left At', 'Notes', 'Marked By', 'Marked At'
            ]);

            // Data rows
            foreach ($participants as $participant) {
                $attendance = $participant->attendances->first();

                fputcsv($file, [
                    $participant->id,
                    $participant->name,
                    $participant->email,
                    $attendance ? $attendance->status : 'Not Marked',
                    $attendance ? $attendance->duration_minutes : 0,
                    $attendance && $attendance->joined_at ? $attendance->joined_at->format('Y-m-d H:i:s') : '',
                    $attendance && $attendance->left_at ? $attendance->left_at->format('Y-m-d H:i:s') : '',
                    $attendance ? $attendance->notes : '',
                    $attendance ? $attendance->marked_by : '',
                    $attendance ? $attendance->updated_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * View course-level attendance report
     */
    public function courseReport($courseId)
    {
        $course = Course::with(['lessons.contents' => function($query) {
            $query->where('attendance_required', true);
        }])->findOrFail($courseId);

        // Get all contents that require attendance
        $attendanceContents = collect();
        foreach ($course->lessons as $lesson) {
            $attendanceContents = $attendanceContents->merge($lesson->contents);
        }

        $participants = $course->participants()->with('attendances')->get();

        return view('attendance.course-report', compact('course', 'attendanceContents', 'participants'));
    }
}
