<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Course;
use App\Models\Discussion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function index(Course $course)
    {
        // ✅ FIX: Use proper discussion authorization instead of course update
        $this->authorize('viewCourseDiscussions', [Discussion::class, $course]);

        $user = Auth::user();

        // Ambil semua ID konten yang ada di dalam kursus ini
        $contentIds = $course->lessons()->with('contents')->get()->pluck('contents.*.id')->flatten();

        // Build base query for discussions
        $discussionsQuery = Discussion::whereIn('content_id', $contentIds)
            ->with(['user', 'replies', 'content.lesson']);

        // Filter discussions by instructor's assigned period participants
        if ($user->isInstructorFor($course) && !Auth::user()->can('manage all courses') && !$user->isEventOrganizerFor($course)) {
            // Get periods where this instructor is assigned for this course
            $instructorPeriods = $user->instructorPeriods()
                ->where('course_id', $course->id)
                ->pluck('course_classes.id');
            
            if ($instructorPeriods->isNotEmpty()) {
                // Get participant IDs only from instructor's assigned periods
                $allowedParticipantIds = User::whereHas('participantPeriods', function ($query) use ($instructorPeriods) {
                    $query->whereIn('course_classes.id', $instructorPeriods);
                })->pluck('id');

                // Also include instructor's own discussions and replies
                $allowedParticipantIds->push($user->id);

                // Filter discussions to only show from allowed users
                $discussionsQuery->whereIn('user_id', $allowedParticipantIds);
            }
        }

        // Ambil semua diskusi yang terkait dengan konten-konten tersebut
        $discussions = $discussionsQuery
            ->latest() // Urutkan dari yang terbaru
            ->paginate(15); // Gunakan paginasi

        return view('discussions.index', compact('course', 'discussions'));
    }

    // Method untuk menyimpan topik diskusi baru
    public function store(Request $request, Content $content)
    {
        // ✅ FIX: Check if user can create discussions for this course
        $course = $content->lesson->course;
        $this->authorize('create', [Discussion::class, $course]);

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $content->discussions()->create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return back()->with('success', 'Topik diskusi berhasil dimulai!');
    }

    // Method untuk menyimpan balasan baru
    public function storeReply(Request $request, Discussion $discussion)
    {
        // ✅ FIX: Check if user can reply to this discussion
        $this->authorize('reply', $discussion);

        $request->validate([
            'body' => 'required|string',
        ]);

        $discussion->replies()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        return back()->with('success', 'Balasan berhasil dikirim!');
    }

    /**
     * Show a specific discussion
     */
    public function show(Discussion $discussion)
    {
        $this->authorize('view', $discussion);

        $discussion->load(['user', 'replies.user', 'content.lesson.course']);

        return view('discussions.show', compact('discussion'));
    }

    /**
     * Update a discussion
     */
    public function update(Request $request, Discussion $discussion)
    {
        $this->authorize('update', $discussion);

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $discussion->update([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return back()->with('success', 'Diskusi berhasil diperbarui!');
    }

    /**
     * Delete a discussion
     */
    public function destroy(Discussion $discussion)
    {
        $this->authorize('delete', $discussion);

        $discussion->delete();

        return back()->with('success', 'Diskusi berhasil dihapus!');
    }
}
