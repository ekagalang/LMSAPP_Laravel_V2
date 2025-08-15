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
            // ✅ FIX: Add 'all' as valid option
            'target_roles.*' => 'string|in:participant,instructor,event-organizer,super-admin,all',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'is_active' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = $request->boolean('is_active', true);

        // ✅ FIX: Handle "all users" selection properly
        if (in_array('all', $request->input('target_roles', []))) {
            $validated['target_roles'] = null; // null means all users
        } else {
            $validated['target_roles'] = $request->input('target_roles');
        }

        Announcement::create($validated);

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    /**
     * ✅ PERBAIKAN: Method show yang benar
     */
    public function show(Announcement $announcement)
    {
        // Mark as read when user views the announcement
        $user = Auth::user();

        // Pastikan AnnouncementRead model exists sebelum menggunakan
        if (class_exists('App\Models\AnnouncementRead')) {
            $announcement->markAsReadBy($user);
        }

        $announcement->load(['user']);

        // Load reads relationship jika table announcement_reads exists
        try {
            $announcement->load(['reads.user']);
        } catch (\Exception $e) {
            // Jika table belum ada, skip loading reads
        }

        return view('admin.announcements.show', compact('announcement'));
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
            // ✅ FIX: Add 'all' as valid option
            'target_roles.*' => 'string|in:participant,instructor,event-organizer,super-admin,all',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        // ✅ FIX: Handle "all users" selection properly
        if (in_array('all', $request->input('target_roles', []))) {
            $validated['target_roles'] = null; // null means all users
        } else {
            $validated['target_roles'] = $request->input('target_roles');
        }

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
     * Index untuk user biasa (semua role bisa akses)
     */
    public function indexForUser(Request $request)
    {
        $user = Auth::user();

        $announcements = Announcement::forUser($user)
            ->with(['user:id,name'])
            ->latest()
            ->paginate(10);

        return view('notifications.index', compact('announcements'));
    }

    /**
     * Get announcements for user - API endpoint untuk notifikasi
     */
    public function getForUser(Request $request)
    {
        $user = Auth::user();

        try {
            $announcements = Announcement::forUser($user)
                ->with(['user:id,name'])
                ->latest()
                ->take(10)
                ->get();

            $unreadCount = 0;
            // Safely get unread count
            try {
                $unreadCount = Announcement::unreadForUser($user)->count();
            } catch (\Exception $e) {
                // If AnnouncementRead table doesn't exist yet, return 0
            }

            return response()->json([
                'announcements' => $announcements->map(function ($announcement) use ($user) {
                    $isRead = false;
                    // Safely check if read
                    try {
                        $isRead = $announcement->isReadByUser($user);
                    } catch (\Exception $e) {
                        // If method doesn't work, assume not read
                    }

                    return [
                        'id' => $announcement->id,
                        'title' => $announcement->title,
                        'content' => $announcement->content,
                        'level' => $announcement->level,
                        'level_color' => $announcement->level_color,
                        'level_icon' => $announcement->level_icon,
                        'author' => $announcement->user->name,
                        'created_at' => $announcement->created_at->diffForHumans(),
                        'formatted_date' => $announcement->created_at->format('d M Y H:i'),
                        'is_read' => $isRead,
                        'is_recent' => $announcement->created_at->gt(now()->subDay()),
                        'url' => route('notifications.index') . '#announcement-' . $announcement->id,
                    ];
                }),
                'unread_count' => $unreadCount,
                'total_count' => $announcements->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'announcements' => [],
                'unread_count' => 0,
                'total_count' => 0,
            ]);
        }
    }

    /**
     * Mark announcement as read for user
     */
    public function markAsRead(Request $request, Announcement $announcement)
    {
        $user = Auth::user();

        try {
            $announcement->markAsReadBy($user);
            return response()->json([
                'success' => true,
                'message' => 'Pengumuman telah ditandai sebagai dibaca.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menandai sebagai dibaca.',
            ], 500);
        }
    }

    /**
     * Mark all announcements as read for user
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();

        try {
            $unreadAnnouncements = Announcement::unreadForUser($user)->get();

            foreach ($unreadAnnouncements as $announcement) {
                $announcement->markAsReadBy($user);
            }

            return response()->json([
                'success' => true,
                'message' => 'Semua pengumuman telah ditandai sebagai dibaca.',
                'marked_count' => $unreadAnnouncements->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menandai semua sebagai dibaca.',
            ], 500);
        }
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount(Request $request)
    {
        $user = Auth::user();

        try {
            $unreadCount = Announcement::unreadForUser($user)->count();
            return response()->json(['unread_count' => $unreadCount]);
        } catch (\Exception $e) {
            return response()->json(['unread_count' => 0]);
        }
    }

    /**
     * Get announcement statistics for admin dashboard
     */
    public function getStats()
    {
        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'total' => 0,
                'active' => 0,
                'published' => 0,
                'recent' => 0,
                'by_level' => [
                    'info' => 0,
                    'success' => 0,
                    'warning' => 0,
                    'danger' => 0,
                ]
            ]);
        }
    }

    private function save(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'level' => ['required', Rule::in(['info', 'success', 'warning', 'danger'])],
            'is_active' => 'boolean',
            'target_roles' => 'nullable|array', // <-- Tambahkan ini
        ]);

        // Jika 'all' dipilih, simpan sebagai null untuk menargetkan semua.
        if (in_array('all', $request->input('target_roles', []))) {
            $validated['target_roles'] = null;
        } else {
            $validated['target_roles'] = $request->input('target_roles');
        }

        $announcement->fill($validated)->save();
    }
}
