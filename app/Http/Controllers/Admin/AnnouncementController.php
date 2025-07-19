<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $availableRoles = [
            'participant' => 'Peserta',
            'instructor' => 'Instruktur',
            'event-organizer' => 'Event Organizer',
            'super-admin' => 'Super Admin'
        ];

        return view('admin.announcements.create', compact('availableRoles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'level' => 'required|in:info,success,warning,danger',
            'target_roles' => 'nullable|array',
            'target_roles.*' => 'string|in:participant,instructor,event-organizer,super-admin',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'is_active' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = $request->boolean('is_active', true);

        Announcement::create($validated);

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function edit(Announcement $announcement)
    {
        $availableRoles = [
            'participant' => 'Peserta',
            'instructor' => 'Instruktur',
            'event-organizer' => 'Event Organizer',
            'super-admin' => 'Super Admin'
        ];

        return view('admin.announcements.edit', compact('announcement', 'availableRoles'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'level' => 'required|in:info,success,warning,danger',
            'target_roles' => 'nullable|array',
            'target_roles.*' => 'string|in:participant,instructor,event-organizer,super-admin',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $announcement->update($validated);

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Toggle active status of announcement
     */
    public function toggleStatus(Announcement $announcement)
    {
        $announcement->update([
            'is_active' => !$announcement->is_active
        ]);

        $status = $announcement->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->back()
            ->with('success', "Pengumuman berhasil {$status}.");
    }

    /**
     * Get announcements for API (used by notification system)
     */
    public function getForUser(Request $request)
    {
        $user = Auth::user();
        $userRoles = $user->getRoleNames()->toArray();

        $announcements = Announcement::active()
            ->where(function ($query) use ($userRoles) {
                $query->whereNull('target_roles');
                foreach ($userRoles as $role) {
                    $query->orWhereJsonContains('target_roles', $role);
                }
            })
            ->with('user:id,name')
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'announcements' => $announcements->map(function ($announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'level' => $announcement->level,
                    'level_color' => $announcement->level_color,
                    'level_icon' => $announcement->level_icon,
                    'author' => $announcement->user->name,
                    'created_at' => $announcement->created_at->diffForHumans(),
                    'is_recent' => $announcement->created_at->gt(now()->subDay()),
                ];
            }),
            'unread_count' => $announcements->where('created_at', '>', now()->subDay())->count(),
        ]);
    }

    /**
     * Mark announcement as read for user
     */
    public function markAsRead(Request $request, Announcement $announcement)
    {
        // In a more complex system, you might want to track individual read status
        // For now, we'll just return success
        return response()->json(['success' => true]);
    }

    /**
     * Get announcement statistics for admin dashboard
     */
    public function getStats()
    {
        $stats = [
            'total' => Announcement::count(),
            'active' => Announcement::where('is_active', true)->count(),
            'published' => Announcement::active()->count(),
            'recent' => Announcement::where('created_at', '>=', now()->subWeek())->count(),
            'by_level' => [
                'info' => Announcement::where('level', 'info')->count(),
                'success' => Announcement::where('level', 'success')->count(),
                'warning' => Announcement::where('level', 'warning')->count(),
                'danger' => Announcement::where('level', 'danger')->count(),
            ]
        ];

        return response()->json($stats);
    }
}
