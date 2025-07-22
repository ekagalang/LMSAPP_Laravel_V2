<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $announcements = Announcement::forUser($user)
            ->latest()
            ->paginate(10);

        // ✅ PERBAIKAN: Hitung jumlah belum dibaca di sini menggunakan accessor dari model User
        $unreadCount = $user->unread_announcements_count;

        return view('announcements.index', compact('announcements', 'unreadCount'));
    }

    public function show(Announcement $announcement)
    {
        $user = Auth::user();
        
        // Tandai sebagai sudah dibaca
        $user->readAnnouncements()->syncWithoutDetaching($announcement->id);

        return view('announcements.show', compact('announcement'));
    }
}
