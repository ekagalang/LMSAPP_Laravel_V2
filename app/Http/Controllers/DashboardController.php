<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        // Menggunakan sistem peran dari Spatie
        if ($user->hasRole('super-admin')) {
            return view('dashboard.admin');
        } elseif ($user->hasRole('instructor')) {
            return view('dashboard.instructor');
        } elseif ($user->hasRole('event-organizer')) {
            // Untuk sementara, EO melihat dasbor yang sama dengan instruktur.
            // Nanti bisa kita buatkan dasbor khusus.
            return view('dashboard.instructor'); 
        } else {
            // Semua peran lain (defaultnya Participant) akan melihat dasbor ini.
            return view('dashboard.participant');
        }
    }
}